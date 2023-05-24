<?php
/**
 * Fractal plugin for Craft CMS 3.x
 *
 * Custom Fractal plugin
 *
 * @link      http://www.dentsucreative.com/
 * @copyright Dentsu Creative UK
 */

namespace dentsucreativeuk\fractal;

use Craft;
use craft\services\Plugins;
use craft\web\twig\variables\CraftVariable;
use craft\events\PluginEvent;
use yii\base\Event;

use dentsucreativeuk\fractal\helpers\FractalTemplateLoader;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Rich @ Mud
 * @package   Fractal
 * @since     1.0.0
 *
 */
class Fractal extends craft\base\Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Fractal::$plugin
     *
     * @var Fractal
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    // public string $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Fractal::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        Craft::info(
            Craft::t(
                'fractal',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function() {
                Craft::$app->getView()->getTwig()->setLoader(new FractalTemplateLoader());
            }
        );
    }
}
