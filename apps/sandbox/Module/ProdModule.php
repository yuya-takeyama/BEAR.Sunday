<?php
/**
 * Module
 *
 * @package    sandbox
 * @subpackage Module
 */
namespace sandbox\Module;

use BEAR\Framework\Module;
use BEAR\Framework\Module\FrameworkModule;
use BEAR\Framework\Module\TemplateEngine;
use BEAR\Framework\Module\Database;
use BEAR\Framework\Module\Provider\CacheProvider;
use Ray\Di\Injector;
use Guzzle\Common\Cache\DoctrineCacheAdapter as CacheAdapter;
use Doctrine\Common\Cache\ApcCache as Cache;

use Ray\Di\AbstractModule;

/**
 * Application module
 *
 * @package    sandbox
 * @subpackage Module
 */
class ProdModule extends AbstractModule
{
    const RESOURCE_CACHE_INTERFACE = 'Guzzle\Common\Cache\CacheAdapterInterface';
    const RESOURCE_CACHE_PROVIDER  = 'BEAR\Framework\Module\Provider\CacheProvider';

    /**
     * App name
     *
     * @var string
     */
    private $app;

    /**
     * Constructor
     *
     * @param string $app
     */
    public function __construct($app, $configFile = 'config.php')
    {
        $this->app = $app;
        $this->properties = require dirname(__DIR__) . "/scripts/{$configFile}";
        parent::__construct();
    }
    
    /**
     * Configure
     *
     * @return void
     */
    protected function configure()
    {
        foreach ($this->properties as $named => $instance) {
            $this->bind('')->annotatedWith($named)->toInstance($instance);
        }
        // install framework module
        $tmpDir = dirname(__DIR__) . '/tmp';
        $logDir = dirname(__DIR__) . '/log';
        $this->install(new FrameworkModule($this->app, $tmpDir, $logDir));

        // install prod module
        $this->install(new TemplateEngine\ProdRendererModule);
        $this->bind(self::RESOURCE_CACHE_INTERFACE)
        ->annotatedWith('resource_cache')
        ->toInstance(new CacheAdapter(new Cache));
        // install application module
        $injector = Injector::create([$this]);
        $this->install(new AppModule($injector));
    }
}
