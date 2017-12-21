<?php
/**
 * This file is part of the <Tool> project.
 *
 * @subpackage   Tool
 * @package    Route
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-02-27
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\ToolBundle\Route;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Config\ConfigCache;

/**
 * route cache management.
 *
 * @subpackage   Tool
 * @package    Route
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class CacheRoute
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $collection;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var array
     */
    private $options = [
            'cache_dir'              => null,
            'generator_class'        => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'generator_base_class'   => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'generator_dumper_class' => 'Symfony\\Component\\Routing\\Generator\\Dumper\\PhpGeneratorDumper',
            'generator_cache_class'  => 'ProjectContainerUrlGenerator',
            'matcher_class'          => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'matcher_base_class'     => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'matcher_dumper_class'   => 'Symfony\\Component\\Routing\\Matcher\\Dumper\\PhpMatcherDumper',
            'matcher_cache_class'    => 'ProjectContainerUrlMatcher',
            'resource_type'          => null,
    ];

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container            = $container;
        $this->options['cache_dir'] = $container->get("kernel")->getCacheDir();
        $this->environment          = ucfirst($container->get("kernel")->getEnvironment());
        $this->collection           = $container->get('router')->getRouteCollection();
        $this->debug                = (bool) $container->get("kernel")->isDebug();
    }

    /**
     * Gets the UrlMatcher instance associated with this Router.
     *
     * @return UrlMatcherInterface A UrlMatcherInterface instance
     */
    public function setGenerator()
    {
        $class      = "app" . $this->environment . ( $this->debug ? 'Debug' : '' ) . $this->options['generator_cache_class'];
        $this->file = realpath($this->options['cache_dir'].'/'.$class.'.php');
        $cache      = new ConfigCache($this->file, false);
        //if (!$cache->isFresh($class)) {
            $dumper     = new $this->options['generator_dumper_class']($this->collection);
            $options     = [
                    'class'      => $class,
                    'base_class' => $this->options['generator_base_class'],
            ];

//            try {
                $cache->write($dumper->dump($options), $this->collection->getResources());
//            } catch (\Exception $e) {
//            }
        //}
    }

    /**
     * Gets the UrlMatcher instance associated with this Router.
     *
     * @return UrlMatcherInterface A UrlMatcherInterface instance
     */
    public function setMatcher()
    {
        $class      = "app" . $this->environment . ( $this->debug ? 'Debug' : '' ) . $this->options['matcher_cache_class'];
        $this->file = realpath($this->options['cache_dir'].'/'.$class.'.php');
        $cache      = new ConfigCache($this->file, false);  // //if (!$cache->isFresh($class))
        //if (!$cache->isFresh($class)) {
            $dumper     = new $this->options['matcher_dumper_class']($this->collection);
            $options     = [
                'class'      => $class,
                'base_class' => $this->options['matcher_base_class'],
            ];
//            try {
                $cache->write($dumper->dump($options), $this->collection->getResources());
//            } catch (\Exception $e) {
//            }
        //}
    }

    /**
     * Checks if the cache is still fresh.
     *
     * This method always returns true when debug is off and the
     * cache file exists.
     *
     * @return Boolean true if the cache is fresh, false otherwise
     */
    protected function isFresh()
    {
        if (!file_exists($this->file)) {
            return false;
        }
        $metadata = $this->file.'.meta';
        if (!file_exists($metadata)) {
            return false;
        }
        $time = filemtime($this->file);
        $meta = unserialize(file_get_contents($metadata));
        foreach ($meta as $resource) {
            //$resource->isFresh($time) :: Returns true if the resource has not been updated since the given timestamp.
            if (!$resource->isFresh($time)) {
                return false;
            }
        }

        return true;
    }
}
