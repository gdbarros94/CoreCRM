<?php 
/**
 * Class PluginHandler
 *
 * This class is responsible for managing plugins within the application.
 * It handles the initialization, loading, and registration of plugins
 * from a specified plugins directory. Each plugin is expected to have
 * a plugin.json file containing metadata and a main.php file for
 * functionality.
 *
 * The PluginHandler maintains a list of active plugins and provides
 * methods to retrieve this list.
 *
 * Usage:
 * - Call `PluginHandler::init()` to initialize and load plugins.
 * - Use `PluginHandler::getActivePlugins()` to retrieve the active plugins.
 */

class PluginHandler
{
    private static $activePlugins = [];

    /**
     * Initialize the PluginHandler.
     *
     * This method loads the plugin directory and its contents.
     *
     * @return void
     */
    public static function init()
    {
        System::log("PluginHandler initialized. Loading plugins...");
        self::loadPlugins();
    }

    /**
     * Load all plugins from the plugins directory.
     *
     * This method checks each folder in the plugins directory for a
     * plugin.json and main.php file. If both are present, it will
     * register the plugin and its routes.
     *
     * @return void
     */
    private static function loadPlugins()
    {
        $pluginsDir = __DIR__ . "/../plugins/";
        if (!is_dir($pluginsDir)) {
            mkdir($pluginsDir, 0777, true);
            System::log("Plugins directory created: {$pluginsDir}");
            return;
        }

        $pluginFolders = array_filter(glob($pluginsDir . "*"), "is_dir");

        foreach ($pluginFolders as $folder) {
            $pluginSlug = basename($folder);
            $pluginJsonPath = $folder . "/plugin.json";
            $mainPhpPath = $folder . "/main.php";

            if (file_exists($pluginJsonPath) && file_exists($mainPhpPath)) {
                $pluginData = json_decode(file_get_contents($pluginJsonPath), true);

                if ($pluginData && isset($pluginData["name"]) && isset($pluginData["slug"])) {
                    // Check if plugin is active (for now, assume all found are active)
                    // In a real scenario, this would come from a database or config
                    self::$activePlugins[$pluginSlug] = $pluginData;
                    System::log("Loading plugin: " . $pluginData["name"] . " ({$pluginSlug})");

                    // Include main.php to register routes, hooks, etc.
                    require_once $mainPhpPath;

                    // Register plugin routes
                    if (isset($pluginData["routes"]) && is_array($pluginData["routes"])) {
                        foreach ($pluginData["routes"] as $route) {
                            // Assuming all plugin routes are GET for now, can be extended
                            RoutesHandler::addRoute("GET", $route, function() use ($pluginSlug, $route) {
                                echo "Plugin {$pluginSlug} route: {$route}";
                            });
                        }
                    }

                } else {
                    System::log("Invalid plugin.json for plugin in folder: {$pluginSlug}", "warning");
                }
            } else {
                System::log("Skipping folder (missing plugin.json or main.php): {$pluginSlug}", "warning");
            }
        }
        System::log("Finished loading plugins.");
    }

    /**
     * Returns an associative array of active plugins, where the keys are the
     * plugin slug and the values are the plugin data from the plugin.json file.
     *
     * @return array
     */
    public static function getActivePlugins()
    {
        return self::$activePlugins;
    }
}


