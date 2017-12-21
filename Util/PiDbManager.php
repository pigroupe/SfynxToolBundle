<?php
/**
 * This file is part of the <Tool> project.
 *
 * @category   Tool
 * @package    Util
 * @subpackage Service
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
namespace Sfynx\ToolBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\ServiceException;
use Sfynx\ToolBundle\Route\AbstractFactory;

/**
 * Instance Db
 *
 * @category   Tool
 * @package    Util
 * @subpackage Service
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class PiDbManager extends AbstractFactory
{
	const BIND_TYPE_NUM = 'NUM';
	const BIND_TYPE_INT = 'INT';
	const BIND_TYPE_STR = 'CHAR';

	/**
	 * @var integer
	 */
	private $InsertId;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * Return the connexion params of a database.
     *
     * @param string $db_name
     * @param string $varName
     * @param string $varValue
     * @param string $var
     * @param string $dataType
     * @access  public
     * @return array
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2013-11-14
     */
    public function addArrayParamsQuery(&$tabParamsquery, $varName, $varValue, $var = 0, $dataType = '')
    {
    	$tabParamsquery[$varName] = array(
    			"NAME" 		=> $varName,
                "VALUE" 	=> $varValue,
				"TYPE" 		=> $dataType
    	);
    }

    /**
     * Execute the request qwith params
     *
     * @param string $query
     * @param array $tableauParams
     * @param int $nb
     * @access  public
     * @return array
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2013-11-14
     */
    public function executeQuery($query, $tabParams = array(), $log = false)
    {
    	if (is_array($tabParams)) {
            foreach ($tabParams as $nom => $param) {
                $nameParam  = $param["NAME"];
                $valueParam = $param["VALUE"];
                $typeParam  = $param["TYPE"];
                if ($typeParam == self::BIND_TYPE_NUM || $typeParam == self::BIND_TYPE_INT) {
                    $query = preg_replace_callback(
                        '/:' . $nameParam . '/i',
                        function($matches) use ($valueParam) {
                            return $valueParam;
                        },
                        $query
                    );
                } elseif ($typeParam == self::BIND_TYPE_STR) {
                    $valueParam = $this->quoteSql($valueParam);
                    $query = preg_replace_callback(
                        '/:' . $nameParam . '/i',
                        function($matches) use ( $valueParam) {
                            return $valueParam;
                        },
                        $query
                    );
                } elseif (is_numeric($valueParam) || ($valueParam == "NULL")) {
                    $query = preg_replace_callback(
                        '/:' . $nameParam . '/i',
                        function($matches) use ($valueParam) {
                            return $valueParam;
                        },
                        $query
                    );
                } else {
                    $valueParam = $this->quoteSql($valueParam);
                    $query = preg_replace_callback(
                        '/:' . $nameParam . '/i',
                        function($matches) use ($valueParam) {
                            return $valueParam;
                        },
                        $query
                    );
                }
            }
    	}

    	str_replace('select', 'select', strtolower($query), $cont_select);
    	str_replace('insert', 'insert', strtolower($query), $cont_insert);

    	if ($log) {
    		print_r($query);
    		return true;
    	}

    	if ($cont_select >= 1) {
    		return $this->getConnection()->executeQuery($query)->fetchAll();
    	} elseif ($cont_insert >= 1) {
    		$this->getConnection()->executeQuery($query);
    		$this->InsertId = $this->getConnection()->lastInsertId();

    		return true;
    	} else {
    		return $this->getConnection()->executeQuery($query)->execute();
    	}
    }

    public function getInsertedId()
    {
    	return $this->InsertId;
    }

    /**
     * Quote SQL
     *
     * @param string $str
     * @access  private
     * @return string
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2013-11-14
     */
    protected function quoteSql($str) {
    	return "'".addslashes($str)."'";
    }

    /**
     * Gets the list of all tables.
     *
     * <code>
     *   $listTableClasses = $this->container->get('bootstrap.database.db')->listTables('table_class');
     *   $listTableClasses = array_combine($listTableClasses, $listTableClasses);
     * </code>
     * @return array    the list of all tables
     * @access public
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since 2014-08-01
     */
    public function listTables($type = 'table_name')
    {
        $tables = array();
        switch ($type) {
            case ('table_name') :
                $tables = $this->getConnection()->getSchemaManager()->listTables();
                break;
            case ('table_class') :
                $meta = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();
                foreach ($meta as $m) {
                    list($domaine, $bundle, $entity) = split("\\\\", $m->getName(), 3);
                	$tables[$m->getName()] = $domaine.$bundle.':'.str_replace('Entity\\', '', $entity);
                }
            	break;
            default :
            	throw ServiceException::optionValueNotSpecified($type);
            	break;
        }

        return $tables;
    }
}
