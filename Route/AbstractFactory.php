<?php
/**
 * This file is part of the <Tool> project.
 *
 * @subpackage   Tool
 * @package    Route
 * @abstract
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-02-03
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\ToolBundle\Route;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

use Sfynx\CoreBundle\Layers\Infrastructure\Exception\ServiceException;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\ExtensionException;
use BeSimple\I18nRoutingBundle\Routing\Router;
use BeSimple\I18nRoutingBundle\Routing\Translator\DoctrineDBALTranslator;

/**
 * Database factory for backup database.
 *
 * @subpackage   Tool
 * @package    Route
 * @abstract
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
abstract class AbstractFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $_container;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $_connection;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_em;

    /**
     * @var \BeSimple\I18nRoutingBundle\Routing\Translator\DoctrineDBALTranslator
     */
    protected $_DoctrineTranslator;

    /**
     * @var \BeSimple\I18nRoutingBundle\Routing\Router
     */
    protected $_RouterTranslator;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;

        //$services_list =  $this->container->getServiceIds();
        //print_r('<PRE>');
        //print_r(var_dump($services_list));
        //print_r('</PRE>');
    }

    /**
     * Gets the connection service
     *
     * @return \Doctrine\DBAL\Connection
     * @access public
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2012-02-03
     */
    public function getConnection()
    {
    	if (empty($this->_connection)) {
    		$this->setConnection();
    	}
    	if ($this->_connection instanceof Connection) {
    		return $this->_connection;
    	}
  		throw ServiceException::serviceNotSupported();
    }

    /**
     * Sets the connection service.
     *
     * @return void
     * @access private
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2012-02-03
     */
    protected function setConnection()
    {
        $this->_connection = $this->getContainer()->get('doctrine.dbal.default_connection');
    }

    /**
     * Gets the getDatabasePlatform service of the connexion
     *
     * @return \Doctrine\DBAL\Platforms\AbstractPlatform
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2012-02-03
     */
    protected function getDatabasePlatform()
    {
        return $this->getConnection()->getDatabasePlatform();
    }

    /**
     * Sets the EntityManager service.
     *
     * @return void
     * @access private
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2012-02-03
     */
    protected function setEntityManager()
    {
        $this->_em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    /**
     * Gets the EntityManager service
     *
     * @return \Doctrine\ORM\EntityManager
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     *@since 2012-02-03
     */
    protected function getEntityManager()
    {
        if (empty($this->_em)) {
            $this->setEntityManager();
        }
        if ($this->_em instanceof EntityManager) {
            return $this->_em;
        }
        throw ServiceException::serviceNotSupported();
    }

    /**
     * Gets the container instance.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function getContainer()
    {
        return $this->_container;
    }

    /**
     * Sets the DoctrineDBALTranslator service.
     *
     * @return void
     * @access private
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2012-02-03
     */
    protected function setDoctrineTranslator()
    {
        $this->_DoctrineTranslator = $this->getContainer()->get('i18n_routing.translator');
    }

    /**
     * Gets the DoctrineDBALTranslator service
     *
     * @return \BeSimple\I18nRoutingBundle\Routing\Translator\DoctrineDBALTranslator
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2012-02-03
     */
    protected function getDoctrineTranslator()
    {
        if (empty($this->_DoctrineTranslator)) {
            $this->setDoctrineTranslator();
        }
        if ($this->_DoctrineTranslator instanceof DoctrineDBALTranslator) {
            return $this->_DoctrineTranslator;
        }
        throw ServiceException::serviceNotSupported();
    }

    /**
     * Sets the Router Translator service.
     *
     * @return void
     * @access private
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2012-02-03
     */
    protected function setRouterTranslator()
    {
        $this->_RouterTranslator = $this->getContainer()->get('be_simple_i18n_routing.router');
    }

    /**
     * Gets the Router Translator service
     *
     * @return \BeSimple\I18nRoutingBundle\Routing\Router
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2012-02-03
     */
    protected function getRouterTranslator()
    {
        if (empty($this->_RouterTranslator)) {
            $this->setRouterTranslator();
        }
        if ($this->_RouterTranslator instanceof Router) {
            return $this->_RouterTranslator;
        }
        throw ServiceException::serviceNotSupported();
    }

    /**
     * Return the token object.
     *
     * @return \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function getToken()
    {
        return $this->_container->get('security.token_storage')->getToken();
    }

    /**
     * Return if yes or no the user is UsernamePassword token.
     *
     * @return boolean
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function isUsernamePasswordToken()
    {
        if ($this->getToken() instanceof \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken) {
            return true;
        }
        return false;
    }

    /**
     * Return the user permissions.
     *
     * @return array    user permissions
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function getUserPermissions()
    {
        return $this->getToken()->getUser()->getPermissions();
    }

    /**
     * Return the user roles.
     *
     * @return array    user roles
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function getUserRoles()
    {
        return $this->getToken()->getUser()->getRoles();
    }
}
