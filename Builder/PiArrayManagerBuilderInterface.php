<?php
/**
 * This file is part of the <Tool> project.
 *
 * @category   Tool
 * @package    Util
 * @subpackage Builder
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

use stdClass;

/**
 * Array builder interface.
 *
 * @category   Tool
 * @package    Util
 * @subpackage Builder
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
interface PiArrayManagerBuilderInterface
{
    public static function recursive_method(array &$array, $method, $option = null, $curlevel=0);
    public static function recursive_method_return(array &$array, $method, $option = null);
    public function TrimArray($Input);
    public static function dump($_ARRAY);
    public static function splitOnValue($array, $value);
    public static function makeHashFromArray($array);
    public static function splitGroups($groups);
    public static function arrayFromGet($getParams);
    public static function writeArray($aInput, $jsVarName, $eol=PHP_EOL);
    public static function InnerHTML($HTML,$Balise,$Prem_val='',$Affiche_prems=true,$Nbre_bal=0);
    public static function findIndice($Tableau,$Val);

    /**
     * Recursively convert a table into a stdClass object.
     *
     * @param array $array
     * @return stdClass
     */
    public static function array_to_object(array $data): stdClass;

    /**
     * Recursively convert a table into a stdClass object.
     *
     * @param stdClass $object
     * @return array
     */
    public static function object_to_array(\stdClass $object): array;
}
