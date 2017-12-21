<?php
/**
 * This file is part of the <Tool> project.
 *
 * @subpackage   Tool
 * @package    Route
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-02-03
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\ToolBundle\Route;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Sfynx\ToolBundle\Route\AbstractFactory;
use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;

use BeSimple\I18nRoutingBundle\Routing\addRouteCollections;

/**
 * route factory.
 *
 * @subpackage   Tool
 * @package    Route
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class RouteTranslatorFactory extends AbstractFactory implements RouteTranslatorFactoryInterface
{
    /** @var RequestStack */
    protected $request = null;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->request = $this->getContainer()->get('request_stack');
    }

    /**
     * {@inheritDoc}
     */
    public function getRefererRoute($langue = '', $options = null, $setLocale = false)
    {
        if ($langue == '')    {
            $langue = $this->request->getCurrentRequest()->getLocale();
        }
        if ($setLocale)    {
            // Record the language
            $this->request->getCurrentRequest()->setLocale($langue);
        }
        // It tries to redirect to the original page.
        $referer  = $this->request->getCurrentRequest()->headers->get('referer');
        $old_url  = str_replace($this->request->getCurrentRequest()->getUriForPath(''), '', $referer);
        $old_info = explode('?', $old_url);
        try {
            $data    = $this->getRouterTranslator()->match($old_info[0]);
            $new_url = $this->getContainer()
                    ->get('router')
                    ->generate($data['_route'], ['locale' => $langue], UrlGeneratorInterface::ABSOLUTE_PATH);
        } catch (\Exception $e) {
            $data    = null;
            $new_url = $referer;
        }
        if (empty($new_url) || ($new_url == "/")) {
            $new_url = $this->getContainer()
                    ->get('router')
                    ->generate('home_page', [], UrlGeneratorInterface::ABSOLUTE_PATH);
        }
    	if (isset($options['result']) && ($options['result'] == 'match')) {
            return $data;
        }

        return $new_url;
    }

    /**
     * {@inheritDoc}
     */
    public function generateLocale($langue = '', $options = null)
    {
        if ($langue == '')    {
            $langue = $this->request->getCurrentRequest()->getLocale();
        }
        $data = $this->getRouterTranslator()->match($this->request->getCurrentRequest()->getPathInfo());
        try {
            $new_url =  $this->getRouterTranslator()->generate($data['_route'], ['locale' => $langue], UrlGeneratorInterface::ABSOLUTE_PATH);
        } catch (\Exception $e) {
            $new_url = $this->request->getCurrentRequest()->getRequestUri();
        }
        if (empty($new_url) || ($new_url == "/")) {
            $new_url =  $this->getRouterTranslator()->generate('home_page', [], UrlGeneratorInterface::ABSOLUTE_PATH);
        }
        if (isset($options['result']) && ($options['result'] == 'match')) {
            return	$data;
        }

        return $new_url;
    }

    /**
     * {@inheritDoc}
     */
    public function generate($route_name = null, $params = null)
    {
        if (!isset($params['locale']) || empty($params['locale'])) {
            $params['locale'] = $this->request->getCurrentRequest()->getLocale();
        }
        if ((null === $route_name))    {
            $route_name = $this->request->getCurrentRequest()->get('_route');
        }
        try {
            $new_url = $this->getRouterTranslator()->generate($route_name, $params, UrlGeneratorInterface::ABSOLUTE_PATH);
        } catch (\Exception $e) {
            unset($params['locale']);
            try {
                $new_url = $this->getRouterTranslator()->generate($route_name, $params, UrlGeneratorInterface::ABSOLUTE_PATH);
            } catch (\Exception $e) {
                try {
                    $new_url = $this->getRouterTranslator()->generate($route_name, $params, UrlGeneratorInterface::ABSOLUTE_PATH);
                } catch (\Exception $e) {
                    $new_url = $this->getRouterTranslator()->generate('home_page', $params, UrlGeneratorInterface::ABSOLUTE_PATH);
                }
            }
        }

        return $new_url;
    }

    /**
     * {@inheritDoc}
     */
    public function getMatchParamOfRoute($param = null, $langue = '', $isGetReferer = false)
    {
        if ($langue == '')    {
            $langue = $this->request->getCurrentRequest()->getLocale();
        }
        $value = null;
        if (isset($_GET[$param])  && !empty($_GET[$param])) {
            $value = $_GET[$param];
        }
        if ((null === $value)) {
            if ($isGetReferer) {
                try {
                    $match = $this->getRefererRoute($langue, ['result' => 'match'], false);
                    $value = $match[$param];
                } catch (\Exception $e) {}
            } else {
                try {
                    $match = $this->generateLocale($langue, ['result' => 'match']);
                    $value = $match[$param];
                } catch (\Exception $e) {}
            }
        }

        return $value;
    }

    /**
     * Gets a new RouteCollection instance.
     *
     * @return \Symfony\Component\Routing\RouteCollection
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2012-02-23
     */
    protected function getCollection()
    {
        return new RouteCollection();
    }

    /**
     * {@inheritDoc}
     */
    public function addRouteCollections($name, array $localesWithPaths, array $defaults = array(), array $requirements = array(), array $options = array(), $host = '', $schemes = array(), $methods = array())
    {
        return (new I18nRouteCollectionBuilder())->buildCollection(
            $name,
            $localesWithPaths,
            $defaults,
            $requirements,
            $options,
            $host,
            $schemes,
            $methods
        );
    }
}
