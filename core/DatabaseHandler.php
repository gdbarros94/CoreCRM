<?php

/**
 * Classe DatabaseHandler
 *
 * Esta classe é responsável por gerenciar a conexão com o banco de dados,
 * incluindo a inicialização da conexão, execução de consultas SQL e
 * manipulação de erros. Ela suporta drivers de banco de dados como MySQL
 * e SQLite.
 *
 * Métodos:
 * - init(): Inicializa o manipulador de banco de dados e estabelece a conexão.
 * - connect(): Estabelece a conexão com o banco de dados com base na configuração.
 * - getConnection(): Retorna a conexão estabelecida com o banco de dados.
 * - query(string $sql, array $params = []): Executa uma consulta SQL com parâmetros.
 */

class DatabaseHandler
{
    private static $connection;
    private static $config;

    /**
     * Inicializa o manipulador de banco de dados. Carrega as configura es
     * de banco de dados do arquivo de configura o e estabelece a conex o
     * com o banco de dados.
     *
     * @return void
     */
    public static function init()
    {
        self::$config = require __DIR__ . "/../config/database.config.php";
        self::connect();
        System::log("DatabaseHandler initialized.");
    }

    /**
     * Estabelece a conex o com o banco de dados. Ele seleciona o driver
     * correto com base na configura o e ajusta as op es de conex o
     * do PDO.
     *
     * @throws Exception Se o driver de banco de dados especificado n o
     *                   for suportado.
     * @throws PDOException Se a conex o com o banco de dados falhar.
     *
     * @return void
     */
    private static function connect()
    {
        $driver = self::$config["driver"];
        $host = self::$config["host"];
        $database = self::$config["database"];
        $username = self::$config["username"];
        $password = self::$config["password"];
        $charset = self::$config["charset"];
        $collation = self::$config["collation"];

        try {
            if ($driver === 'mysql') {
                $dsn = "mysql:host={$host};dbname={$database};charset={$charset}";
                self::$connection = new PDO($dsn, $username, $password);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$connection->exec("SET NAMES '{$charset}' COLLATE '{$collation}'");
            } else if ($driver === 'sqlite') {
                $dsn = "sqlite:" . __DIR__ . "/../" . $database; // Assuming database is a file path for SQLite
                self::$connection = new PDO($dsn);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } else {
                throw new Exception("Unsupported database driver: {$driver}");
            }
            System::log("Database connection established successfully with driver: {$driver}.");
        } catch (PDOException $e) {
            System::log("Database connection failed: " . $e->getMessage(), "error");
            die("Database connection failed: " . $e->getMessage());
        } catch (Exception $e) {
            System::log("Database error: " . $e->getMessage(), "error");
            die("Database error: " . $e->getMessage());
        }
    }

    /**
     * Retorna a conex o estabelecida com o banco de dados.
     *
     * @return PDO A conex o estabelecida com o banco de dados.
     */
    public static function getConnection()
    {
        return self::$connection;
    }

    /**
     * Executa uma consulta SQL com par metros.
     *
     * @param string $sql A consulta SQL a ser executada.
     * @param array $params Os par metros da consulta SQL.
     *
     * @return PDOStatement O objeto com a consulta executada.
     */
    public static function query($sql, $params = [])
    {
        global $config;
        if ($config["debug"]) {
            System::log("SQL Query: " . $sql . " Params: " . json_encode($params), "debug");
        }
        $stmt = self::$connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}

class QueryBuilder
{
    private $table;
    private $query = "";
    private $bindings = [];

    /**
     * Cria um objeto QueryBuilder para uma tabela especificada.
     *
     * @param string $table A tabela a ser manipulada.
     */
    public function __construct($table)
    {
        $this->table = $table;
    }
    
    /**
     * Adiciona um trecho "SELECT"  `a consulta. Pode receber um array de colunas
     * a serem selecionadas ou nenhuma coluna (padr o), em que caso todas as
     * colunas s o selecionadas.
     *
     * @param array $columns As colunas a serem selecionadas (padr o: ["*"])
     * 
     * @return QueryBuilder O pr prio objeto QueryBuilder, para chaining.
     */
    public function select($columns = ["*"])
    {
        $this->query = "SELECT " . implode(", ", $columns) . " FROM {$this->table}";
        return $this;
    }

    /**
     * Adiciona um trecho "WHERE"  `a consulta.  s  poss vel
     * adicionar uma condi o de busca com operadores de compara o
     * (ex: =, <, >, LIKE, etc.).
     *
     * Caso n o haja um "WHERE" na consulta, este m todo o
     * adiciona. Caso contr rio,  adicionado um "AND" antes da
     * nova condi o.
     *
     * @param string $column A coluna a ser utilizada na condi o
     * @param string $operator O operador de compara o a ser usado
     * @param mixed $value O valor a ser comparado na condi o
     *
     * @return QueryBuilder O pr prio objeto QueryBuilder, para chaining.
     */
    public function where($column, $operator, $value)
    {
        if (strpos($this->query, "WHERE") === false) {
            $this->query .= " WHERE ";
        } else {
            $this->query .= " AND ";
        }
        $this->query .= "{$column} {$operator} ?";
        $this->bindings[] = $value;
        return $this;
    }

    /**
     * Executa um INSERT na tabela especificada.
     * 
     * @param array $data Os dados a serem inseridos. As chaves do array
     *            devem ser os nomes das colunas, e os valores devem ser
     *            os valores a serem inseridos.
     * 
     * @return QueryBuilder O pr prio objeto QueryBuilder, para chaining.
     */
    public function insert($data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $this->query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->bindings = array_values($data);
        return $this;
    }

    /**
     * Executa um UPDATE na tabela especificada.
     * 
     * @param array $data Os dados a serem atualizados. As chaves do array
     *            devem ser os nomes das colunas, e os valores devem ser
     *            os valores a serem atualizados.
     * 
     * @return QueryBuilder O pr prio objeto QueryBuilder, para chaining.
     */
    public function update($data)
    {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "{$column} = ?";
            $this->bindings[] = $value;
        }
        $this->query = "UPDATE {$this->table} SET " . implode(", ", $set);
        return $this;
    }


    /**
     * Executa um DELETE na tabela especificada.
     * 
     * @return QueryBuilder O pr prio objeto QueryBuilder, para chaining.
     */
    public function delete()
    {
        $this->query = "DELETE FROM {$this->table}";
        return $this;
    }

    /**
     * Executa a consulta SQL e retorna todos os resultados.
     *
     * @return array Os resultados da consulta.
     */
    public function get()
    {
        $stmt = DatabaseHandler::query($this->query, $this->bindings);
        return $stmt->fetchAll();
    }

    /**
     * Executa a consulta SQL e retorna o objeto PDOStatement.
     * 
     * @return PDOStatement O objeto com a consulta executada.
     */
    public function execute()
    {
        return DatabaseHandler::query($this->query, $this->bindings);
    }
}



//$query = new QueryBuilder("users");
//$result = $query->select()->where("id", "=", 1)->get();
//$insert = $query->insert(["name" => "John Doe", "email" => "BwK8B@example.com"])->execute();