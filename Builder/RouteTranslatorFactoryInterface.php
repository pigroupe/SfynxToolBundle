<?php
/**
 * This file is part of the <Tool> project.
 *
 * @category   Tool
 * @package    Util
 * @subpackage Route
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\ToolBundle\Builder;

use Symfony\Component\Routing\RouteCollection;

/**
 * RouteTranslatorFactoryInterface interface.
 *
 * @category   Tool
 * @package    Util
 * @subpackage Route
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
interface RouteTranslatorFactoryInterface
{
    /**
     * Return the referer url translated to the locale value and record the language in session..
     *
     * <code>
     *     $match = $this->container->get('sfynx.tool.route.factory')->getRefererRoute($this->container->get('request_stack')->getCurrentRequest()->getLocale(), array('result' => 'match'));
     *     $url   = $this->container->get('sfynx.tool.route.factory')->getRefererRoute($this->container->get('request_stack')->getCurrentRequest()->getLocale());
     * </code>
     *
     * @param string $langue
     * @param array $options
     * @return string the url translated to the locale value.
     * @access public
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2012-02-23
     */
    public function getRefererRoute($langue = '', $options = null);

    /**
     * Return the current url translated to the locale value.
     *
     * <code>
     *     $match	= $this->container->get('sfynx.tool.route.factory')->generateLocale($this->container->get('request_stack')->getCurrentRequest()->getLocale(), array('result' => 'match'));
     *     $url    	= $this->container->get('sfynx.tool.route.factory')->generateLocale($this->container->get('request_stack')->getCurrentRequest()->getLocale(), array('result' => 'string'));
     * </code>
     *
     * @param string $langue
     * @param array $options
     *
     * @return string the url translated to the locale value.
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since  2012-02-23
     */
    public function generateLocale($langue = '', $options = null);

    /**
     * Return the url translated by route name to the locale value.
     *
     * <code>
     *     $url = $this->container->get('sfynx.tool.route.factory')->generate("page_lamelee_connexion", array('locale'=> $this->container->get('session')->getLocale()));
     * </code>
     *
     * @param string $route_name
     * @param array  $params
     *
     * @return string the url translated to the locale value.
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since  2012-04-11
     */
    public function generate($route_name = null, $params = null);

    /**
     * Return the value of the parameter of the current or the referer url.
     *
     * <code>
     *     $slug = $this->container->get('sfynx.tool.route.factory')->getMatchParamOfRoute('slug', $this->container->get('session')->getLocale(), 0);
     *     $route = $this->container->get('sfynx.tool.route.factory')->getMatchParamOfRoute('_route', $this->container->get('session')->getLocale(), 1);
     * </code>
     *
     * @param array   $param
     * @param string  $lang
     * @param boolean $isGetReferer
     *
     * @return string the url translated to the locale value.
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since  2012-04-11
     */
    public function getMatchParamOfRoute($param = null, $lang = '', $isGetReferer = false);

    /**
     * Add in RouteCollection.
     *
     * <code>
     *  $names = 'public_homepage';
     *  $locales =  array(
     *              'en' => '/welcome',
     *              'fr' => '/bienvenue',
     *              'de' => '/willkommen',
     *           ),
     * <code>
     *
     * @param  string          $name             The route name
     * @param  array           $localesWithPaths An array with keys locales and values path patterns
     * @param  array           $defaults         An array of default parameter values
     * @param  array           $requirements     An array of requirements for parameters (regexes)
     * @param  array           $options          An array of options
     * @param  string          $host             The host pattern to match
     * @param  string|array    $schemes          A required URI scheme or an array of restricted schemes
     * @param  string|array    $methods          A required HTTP method or an array of restricted methods
     *
     * @return \Symfony\Component\Routing\RouteCollection
     * @access private
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since  2012-02-23
     *
     * // googd: 'home_page' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'PiApp\\AdminBundle\\Controller\\FrontendController::pageAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/',    ),  ),  4 =>   array (  ),),
     * // bad :  'home_page' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'PiApp\\AdminBundle\\Controller\\FrontendController::pageAction',  ),  2 =>   array (    '_method' => 'GET|POST',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/',    ),  ),  4 =>   array (    0 =>     array (      0 => 'text',      1 => '1',    ),  ),),
     */
    public function addRouteCollections($name, array $localesWithPaths, array $defaults = array(), array $requirements = array(), array $options = array(), $host = '', $schemes = array(), $methods = array());
}
