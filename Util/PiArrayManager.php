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

use stdClass;
use Sfynx\ToolBundle\Builder\PiArrayManagerBuilderInterface;

/**
 * Description of array manager
 *
 * <code>
 *     $ArrayFormatter = $container->get('sfynx.tool.array_manager');
 *     $result         = $ArrayFormatter->dump($array); // obtains a datetime instance
 * </code>
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
class PiArrayManager implements PiArrayManagerBuilderInterface
{

   /**
     * set recursivly a method in value of a table.
     *
     * <code>
     * 		$this->container->get('sfynx.tool.array_manager')->recursive_method($params, 'krsort');
     * </code>
     *
     * @param    array        $array
     * @param    string        $method
     * @param    integer        level
     * @return array
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public static function recursive_method(array &$array, $method, $option = null, $curlevel=0)
    {
        foreach ($array as $k=>$v) {
            if (is_array($v)) {
                self::recursive_method($v, $method, $option, $curlevel+1);
            } elseif ((null === $option)) {
                $method($array);
            } else {
            	$method($array, $option);
            }
        }
    }

    /**
     * array_map a entire array recursivly.
     *
     * <code>
     * 		$this->get("sfynx.tool.array_manager")->recursive_method_return($params, 'array_change_key_case', CASE_UPPER);
     * </code>
     *
     * @param array  $array
     * @param string $method
     *
     * @return array
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public static function recursive_method_return(array &$array, $method, $option = null)
    {
    	if (!(null === $option)) {
    		$array = $method($array, $option);
    	} else {
    		$array = $method($array);
    	}
		foreach ($array as $key => $value) {
			if ( is_array($value) ) {
				$array[$key] = self::recursive_method_return($value, $method, $option);
			}
		}
		return $array;
    }

    /**
     * Trims a entire array recursivly.
     *
     * @param array $Input Input array
     *
     * @return array
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function TrimArray($Input)
    {
        if (!is_array($Input))
            return trim($Input);

        return array_map(array($this, 'TrimArray'), $Input);
    }

    /**
     * dumps the table
     *
     * @param array $_ARRAY
     * @return string
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public static function dump($_ARRAY)
    {
        print_r('<pre>');
        print_r($_ARRAY);
        print_r('</pre>');
    }

    /**
     * finds the selected value, then splits the array on that key, and returns the two arrays
     * if the value was not found then it returns false
     *
     * @param array $array
     * @param string $value
     * @return mixed
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public static function splitOnValue($array, $value)
    {
        if (is_array($array)) {
            $paramPos = array_search($value, $array);

            if ($paramPos) {
                $arrays[] = array_slice($array, 0, $paramPos);
                $arrays[] = array_slice($array, $paramPos + 1);
            } else {
                $arrays = null;
            }
            if (is_array($arrays)) {
                return $arrays;
            }
        }
        return null;
    }

    /**
     * takes a simple array('value','3','othervalue','4')
     * and creates a hash using the alternating values:
     * array(
     *  'value' => 3,
     *  'othervalue' => 4
     * )
     *
     * @param array $array
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public static function makeHashFromArray($array)
    {
        $hash = null;

        if (is_array($array) && count($array) > 1) {
            for ($i = 0, $size = count($array); $i <= $size; $i+= 2) {
                if (isset($array[$i])) {
                    $key = $array[$i];
                    $value = $array[$i + 1];
                    if (!empty($key) && !empty($value)) {
                        $hash[$key] = $value;
                    }
                }
            }
        }

        if (is_array($hash)) {
            return $hash;
        }
    }

    /**
     * takes an array:
     * $groups = array(
     *     'group1' => "<h2>group1......",
     *     'group2' => "<h2>group2...."
     *     );
     *
     * and splits it into 2 equal (more or less) groups
     * @param unknown_type $groups
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public static function splitGroups($groups)
    {
        foreach ($groups as $k => $v) {
            //set up an array of key = count
            $g[$k] = strlen($v);
            $totalItems += $g[$k];
        }

        //the first half is the larger of the two
        $firstHalfCount = ceil($totalItems / 2);

        //now go through the array and add the items to the two groups.
        $first=true;
        foreach ($g as $k => $v) {
            if ($first) {
                $arrFirst[$k] = $groups[$k];
                $count += $v;
                if ($count > $firstHalfCount) {
                    $first = false;
                }
            } else {
                $arrSecond[$k] = $groups[$k];
            }
        }

        $arrReturn['first']=$arrFirst;
        $arrReturn['second']=$arrSecond;
        return $arrReturn;
    }

    /**
     * this function builds an associative array from a standard get request string
     * eg: animal=dog&sound=bark
     * will return
     * array(
     *     animal => dog,
     *     sound => bark
     * )
     *
     * @param string $getParams
     * @return array
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public static function arrayFromGet($getParams)
    {
        $parts = explode('&', $getParams);
        if (is_array($parts)) {
            foreach ($parts as $part) {
                $paramParts = explode('=', $part);
                if (is_array($paramParts) && count($paramParts) == 2) {
                    $param[$paramParts[0]] = $paramParts[1];
                    unset($paramParts);
                }
            }
        }
        return $param;
    }

    /**
     * Convertir un tableau PHP en Javascript
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public static function writeArray($aInput, $jsVarName = "name", $eol = PHP_EOL)
    {
        $js = $jsVarName.'=new Array();'.$eol;
        foreach ($aInput as $key => $value) {
            if (!is_numeric($key)) {
                $key = '"'.$key.'"';
            }
            if (is_array($value)) {
                $js .= self::writeArray($value, $jsVarName.'['.$key.']', $eol);
            } else {
                if ((null === $value)) {
                    $value='null';
                } elseif (is_bool($value)) {
                    $value = ($value) ? 'true' : 'false';
                } elseif (!is_numeric($value)) {
                    $value = '"'.$value.'"';
                }
                $js .= $jsVarName.'['.$key.']='.$value.';'.$eol;
            }
        }
        return $js;
    }

    /**
     * Convert an array to string.
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public static function convertArrayToString($aInput, $translator, $prefix = 'pi.form.label.field.', $VarName = "", $eol=PHP_EOL)
    {
        $js = '';
        foreach ($aInput as $key => $value) {
            if (!is_numeric($key)) {
                $key = ''.$key.'';
            } else {
                $key = ''.($key+1).'';
            }
            if (is_array($value)) {
                $js .= self::convertArrayToString($value, $translator, $prefix, $VarName.' '.$translator->trans($prefix.$key).' > ', $eol);
            } else {
                if ((null === $value)) {
                    $value='null';
                } elseif (is_bool($value)) {
                    $value = ($value) ? 'true' : 'false';
                } elseif (!is_numeric($value)) {
                	$value = $translator->trans($value);
                }
                $js .= $VarName.' '.$value.$eol;
            }
        }
        return str_replace($prefix, '', $js);
    }

    /**
     * Convert a php array to a string array
     *
     * @param array $args
     * @param string $result
     * @static
     * @return string
     */
    public static function recursiveArrayToString(array $args = [], string $result = '')
    {
        $result .= '[';
        foreach ($args as $k => $value) {
            $k = \is_string($k) ? "'$k'" : $k;
            $value = \is_string($value) ? "'$value'" : $value;
            $value = \is_array($value) ? self::recursiveArrayToString($value): $value;

            $result .= "$k => " . $value . ', ';
        }
        $result .= ']';

        return $result;
    }

    /**
     * Extraction de contenu d'un tableau HTML.
     *
     * @param string    $HTML            code html contenant la table dont on veut extraire la valeur des cellules
     * @param string    $Balise    nom     de la balise dont on veut extraire le contenu
     * @param string    $Prem_val        valeur qui est prise pour déterminer une nouvelle ligne
     * @param boolean    $Affiche_prems    permet d'inclure (true) ou non (false) dans le tableau d'extraction $Prem_val si cette option est prise.
     * @param int        $Nbre_bal        détermine le nombre de contenus de balise composant une ligne.
     * @return array
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public static function InnerHTML($HTML,$Balise,$Prem_val='',$Affiche_prems=true,$Nbre_bal=0)
    {
        /*
         $HTML contient le code html contenant la table dont on veut extraire la valeur des cellules

        $Balise correspond à la balise dont on veut extraire le contenu.
        Ne pas mettre les "<" et ">".
        CONTRAINTE : dans le code HTML, la balise doit être collée à "<"
        <td... est correct et <          td... ne l'est pas.

        $Prem_val correspond à la valeur qui est prise pour déterminer une nouvelle ligne.
        InnerHTML est INSENSIBLE à la casse concernant $Prem_val. Pour changer cela remplacer les stripos par strpos.

        $Affiche_prems permet d'inclure (true) ou non (false) dans le tableau d'extraction $Prem_val si cette option est prise.

        $Nbre_bal détermine le nombre de contenus de balise composant une ligne.
        Si $Prem_val et $Nbre_bal sont renseignés, c'est $Prem_val qui prime.

        Si $Nbre_bal est supérieur à 0, $Affiche_prems est toujours true

        Sachant qu'entre <td> et </td> il peut y avoir en théorie n'importe quoi (autres balises, saut de ligne...), on ne peut pas utiliser <td>([[:alnum:]]+)</td> pour le masque

        Si on utilise <td>(.+)</td> ça ne marche pas non plus car la partie extraite dans un preg_match_all sera celle comprise entre le premier <td> et le dernier </td>.
        Tous les autres couples <td>...</td> ne seront pas pris en compte.

        On décompose donc l'extraction.*/

        if (strlen($Prem_val)===0) {
            //On va extraire les valeurs par nombre de ligne
                        //==>Si $Nbre_bal n'est pas renseigné il faut qu'il soit supérieur à 0
            $Affiche_prems=true;   //par contre, s'il est renseigné on garde la valeur passée en paramètre.
            if ($Nbre_bal===0) {
                $Nbre_bal=1;
            }
        } else {
            //Cela signifie que on va extraire les ligne par une valeur

            $Nbre_bal=0;//Si $Nbre_bal a malencontreusement été renseigné en même temps que $Prem_val,
        }//On remet cette variable à 0 pour que l'extraction par valeur soit détectée (confère **)

        $Compteur=$Nbre_bal+1;
        preg_match_all('~<'.$Balise.'[^>]*>~is',$HTML,$Deb_balise);

        foreach($Deb_balise[0] as $Val)
        {
            $Val=substr($HTML,strpos($HTML,$Val)+strlen($Val));//On enlève tout ce qu'il y a avant le <td> y compris ce dernier
            $Temp=substr($Val,0,stripos($Val,'</'.$Balise.'>'));
            $HTML=substr($HTML,stripos($HTML,'</'.$Balise.'>')+strlen($Balise));//On supprime le code <td>...</td> que l'on vient d'extraire
            $Taille=count($Recup)-1;

            if ($Nbre_bal>0)//**On traite une ligne par nombre de balise)
            {
                if ($Compteur<$Nbre_bal){
                    $Recup[$Taille][]=$Temp;
                    ++$Compteur;
                } else {
                    $Recup[$Taille+1][]=$Temp;
                    $Compteur=1;
                }
            }
            else//Par valeur de balise commune
            {
                if ($Taille===-1)$Taille=0;//C'est que $Prem_val n'est pas en première position

                if (stripos($Temp,$Prem_val)===false)
                {
                    if (count($Recup[$Taille])===0)
                        $Taille2=0;
                    else
                        $Taille2=count($Recup[$Taille])-1;

                    if ($Recup[$Taille][$Taille2]===NULL)
                        $Recup[$Taille][$Taille2]=$Temp;
                    else
                        $Recup[$Taille][]=$Temp;
                } else {
                    if ($Compteur==1)
                        $Compteur=0;
                    else
                        $Compteur=$Taille+1;

                    if ($Affiche_prems===true)
                        $Recup[$Compteur][]=$Temp;
                    else
                        $Recup[$Compteur][]=NULL;
                    /*
                    if ($Compteur==1)
                        $Compteur=0;
                    else
                        $Compteur=$Taille+1;

                    Pour pouvoir insèrer des contenus de balise se trouvant avant $Prem_val si cette valeur n'est pas en première position,
                    on a forcé la taille à 0.
                    Si $Prem_val est en première position dans le tableau renvoyé dans $Deb_balise[0] alors $Taille, au lieu d'être à  0 sera à 1
                    Pour corriger cela on fait tourner le $Compteur.
                    Ainsi, si la première valeur de $Deb_balise[0] est égale à $Prem_val, le premier indice du tableau de résultat sera 0.
                    */
                }
                ++$Compteur;
            }
        }
        return $Recup;
    }

    /**
     * Cette fonction sert à trouver tous les indices dans un tableau multidimentionnel pour localiser une valeur dans ce tableau
     *
     * @param array        $Tableau
     * @param string    $Val
     * @return array
     */
    public static function findIndice($Tableau,$Val)
    {
        static $Drapeau=false;static $Compteur=false;static $Result=array(); static $Tbl_origine=array();

        if ($Compteur==false)    //$Compteur permet d'assigner à $Tbl_origine le tableau passé en paramètre lors du premier appel de la fonction
        {                       //En effet, lorsque l'on trouve la valeur le tableau en cours est celui contenant cette valeur.
            $Tbl_origine=$Tableau;
            $Compteur=true;
        }

        if (in_array($Val,$Tableau))
        {
            $Drapeau    = true;
            $Result[]    = array_search($Val,$Tableau);
            /*On récupère le chemin d'indexation menant à la valeur
             Afin d'éviter Fatal error: Cannot use string offset as an array in..., on :

            - Inverse le tableau de résultats
            - Remonte l'arborescence jusqu'à trouver l'indice "racine" menant à la valeur
            - On renvoie la chaine de caractères composée */

            $Result = array_reverse($Result);

            foreach($Result as $Check){
                //Pour gérer les indices associatifs
                $Guillemets    = gettype($Check)=="string" ? "'" : "";
                $Code        = "[".$Guillemets.$Check.$Guillemets."]".$Code;

                eval('$Test=$Tbl_origine'.$Code.';');
                if ($Test==$Val) break;
            }
            return $Code;
        }

        foreach($Tableau as $Cle=>$Valeur) {
            if ($Drapeau==true) break;//break; Pour remonter l'arborescence d'appel de la fonction en gardant le résultat

            if (is_array($Valeur)) {
                $Result[]=$Cle; //$Result[]=$Cle: On rajoute l'indice parcouru dans le tableau de résultats
                $Result=self::findIndice($Valeur,$Val);
            }
        }
        return $Result;
    }

    /**
     * Recursively convert a table into a stdClass object.
     *
     * @param array $array
     * @return stdClass
     */
    public static function array_to_object(array $data): stdClass
    {
        return json_decode(json_encode($data), false);
    }

    /**
     * Recursively convert a table into a stdClass object.
     *
     * @param stdClass $object
     * @return array
     */
    public static function object_to_array(\stdClass $object): array
    {
        return json_decode(json_encode($object), true);
    }

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function array_merge_recursive_distinct (array $array1, array $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array ($value)
                && isset ($merged[$key])
                && is_array ($merged[$key])
            ) {
                $merged[$key] = self::array_merge_recursive_distinct($merged [$key], $value);
            } else {
                $value = in_array(strtolower(trim($value)), ['false']) ? false : $value;
                $value = in_array(strtolower(trim($value)), ['true']) ? true : $value;

                $merged[$key] = $value;
            }
        }

        return $merged;
    }

}
