<?php
/**
 * sandbox
 *
 * @package App.sandbox
 */
namespace sandbox;

use BEAR\Framework\Framework;
use BEAR\Framework\Module\FrameworkModule;
use BEAR\Framework\AbstractAppContext;
use Ray\Di\Injector;

require_once dirname(dirname(__DIR__)) . '/vendor/smarty/smarty/libs/Smarty.class.php';

/**
 * Applicaton
 *
 * @package sandbox
 */
final class App extends AbstractAppContext
{
    /** Version @var string */
    const VERSION = '0.1.0';

    /** Name @var string */
    const NAME = __NAMESPACE__;

    /** Path @var string */
    const DIR = __DIR__;

    /** Run mode Production */
    const RUN_MODE_PROD = 0;

    /** Run mode Develop */
    const RUN_MODE_DEV = 1;
    
    /** Run mode Develop */
    const RUN_MODE_DEV_CACHE = 2;

    /**
     * Return application instance
     *
     * @param integer $runMode
     */
    public static function factory($runMode = self::RUN_MODE_PROD)
    {
        // configure framework
        $framework = (new Framework)->setLoader(__NAMESPACE__, __DIR__)->setExceptionHandler();
        
        // configure application
        $cacheKey = __NAMESPACE__ . $runMode . filemtime(dirname(__DIR__));
        $useCache = (! $runMode);
        if ((! $runMode) && apc_exists($cacheKey)) {
            $app = apc_fetch($cacheKey);
            return $app;
        }
        
        // run mode
        switch ($runMode) {
            case self::RUN_MODE_DEV:
                apc_clear_cache();
                apc_clear_cache('user');
            case self::RUN_MODE_DEV_CACHE:
                $modeModule = new Module\DevModule;
                break;
            case self::RUN_MODE_PROD:
            default:
                $modeModule = new Module\ProdModule;
        }
        $injector = Injector::create([new FrameworkModule(__CLASS__), new $modeModule, new Module\AppModule], true);
        $app = $injector->getInstance(__CLASS__);
        if ($useCache) {
            apc_store($cacheKey, $app);
        }
        return $app;
    }
}