<?php
/**
 * This file is part of the <Tool> project.
 *
 * @subpackage   Tool
 * @package    Extension
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-03
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\ToolBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Action Functions used in twig
 *
 * @subpackage   Tool
 * @package    Extension
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class PiForwardExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The service container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     * @access public
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getName()
    {
        return 'sfynx_tool_forward_extension';
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * <code>
     *  {{ renderForward('MyBundle:MyController:Myaction') }}
     * </code>
     *
     * @return array An array of functions
     * @access public
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('renderForward', [$this, 'renderForwardFunction']),
        ];
    }

    /**
     * Returns the Response content for a given controller or URI.
     *
     * @param string $controller The controller name
     * @param array  $params    An array of params
     *
     * @return string
     * @access public
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function renderForwardFunction($controller, $params = array())
    {
        $params['lang']   = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
        $params['_route'] = $this->container->get('request_stack')->getCurrentRequest()->get('_route');
        // this allow Redirect Response in controller action
        $params['_controller'] = $controller;
        $subRequest = $this->container->get('request_stack')->getCurrentRequest()->duplicate($_GET, $_POST, $params);
        $response   =  $this->container->get('http_kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

        return $response->getContent();
    }
}
