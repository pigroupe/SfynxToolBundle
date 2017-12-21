<?php
/**
 * This file is part of the <Tool> project.
 *
 * @subpackage   Tool
 * @package    Extension
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-11
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\ToolBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\ServiceException;

/**
 * Tool Filters and Functions used in twig
 *
 * @subpackage   Tool
 * @package    Extension
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class PiToolExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

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
    public function getName() {
        return 'sfynx_tool_tool_extension';
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * <code>
     *  {{ comment.content|html }}
     *  {{ 'pi.page.translation.title'|trans|limite('0', 25) }}
     *  {{ "%s Result"|translate_plural("%s Results", entitiesByMonth|count) }}
     *
     *  <span class="hiddenLink {{ url|obfuscateLink }}">
     *  {{ obfuscateLinkJS('a', 'hiddenLink') }}
     *
     * </code>
     *
     * @return array An array of filters
     * @access public
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getFilters() {
        return [

            // default
            new \Twig_SimpleFilter('php_funct', [$this, 'phpFilter']),

            // debug
            new \Twig_SimpleFilter('dump', [$this, 'dumpFilter']),
            new \Twig_SimpleFilter('print_r', [$this, 'print_rFilter']),
            new \Twig_SimpleFilter('get_class', [$this, 'get_classFilter']),

            // markup
            new \Twig_SimpleFilter('nl2br', [$this, 'nl2brFilter']),
            new \Twig_SimpleFilter('joinphp', [$this, 'joinphpFilter']),

            // escape
            new \Twig_SimpleFilter('htmlspecialchars', [$this, 'htmlspecialcharsFilter']),
            new \Twig_SimpleFilter('addslashes', [$this, 'addslashesFilter']),
            new \Twig_SimpleFilter('htmlentities', [$this, 'htmlentitiesFilter']),

            // text
            new \Twig_SimpleFilter('substr', [$this, 'substrFilter']),
            new \Twig_SimpleFilter('ucfirst', [$this, 'ucfirstFilter']),
            new \Twig_SimpleFilter('ucwords', [$this, 'ucwordsFilter']),
            new \Twig_SimpleFilter('cleanWhitespace', [$this, 'cleanWhitespaceFilter']),
            new \Twig_SimpleFilter('sanitize', [$this, 'sanitizeFilter']),
            new \Twig_SimpleFilter('slugify', [$this, 'slugifyFilter']),
            new \Twig_SimpleFilter('departement', [$this, 'departementFilter']),

            new \Twig_SimpleFilter('limite', [$this, 'limitecaractereFilter']),
            new \Twig_SimpleFilter('splitText', [$this, 'splitTextFilter']),
            new \Twig_SimpleFilter('splitHtml', [$this, 'splitHtmlFilter']),
            new \Twig_SimpleFilter('truncateText', [$this, 'truncateFilter']),
            new \Twig_SimpleFilter('cutText', [$this, 'cutTextFilter']),
            new \Twig_SimpleFilter('renderResponse', [$this, 'renderResponseFilter']),

            //array
            new \Twig_SimpleFilter('count', [$this, 'countFilter']),
            new \Twig_SimpleFilter('reset', [$this, 'resetFilter']),
            new \Twig_SimpleFilter('steps', [$this, 'stepsFilter']),
            new \Twig_SimpleFilter('sliceTab', [$this, 'arraysliceFilter']),
            new \Twig_SimpleFilter('end', [$this, 'endFilter']),
            new \Twig_SimpleFilter('XmlString2array', [$this, 'XmlString2arrayFilter']),
            new \Twig_SimpleFilter('orderBy', [$this, 'orderByFilter']),
            new \Twig_SimpleFilter('unset', [$this, 'unsetFilter']),

            //translation
            new \Twig_SimpleFilter('translate_plural', [$this, 'translatepluralFilter']),
            new \Twig_SimpleFilter('pluralize', [$this, 'pluralizeFilter']),
            new \Twig_SimpleFilter('depluralize', [$this, 'depluralizeFilter']),

            // cryptage
            new \Twig_SimpleFilter('encrypt', [$this, 'encryptFilter']),
            new \Twig_SimpleFilter('decrypt', [$this, 'decryptFilter']),
            new \Twig_SimpleFilter('obfuscateLink', [$this, 'obfuscateLinkFilter']),

            // status
            new \Twig_SimpleFilter('status', [$this, 'statusFilter']),
        ];
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * <code>
     *  {{ link(label, path, array('style' = >'width:11px')) }}
     * </code>
     *
     * @return array An array of functions
     * @access public
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getFunctions() {
        return [
            // Php Function
            new \Twig_SimpleFunction('file_exists'),
            new \Twig_SimpleFunction('file_get_contents'),
            //
            new \Twig_SimpleFunction('link', [$this, 'linkFunction']),
            new \Twig_SimpleFunction('get_img_flag_By_country', [$this, 'getImgFlagByCountryFunction']),
            new \Twig_SimpleFunction('get_pattern_by_local', [$this, 'getDatePatternByLocalFunction']),
            new \Twig_SimpleFunction('clean_name', [$this, 'getCleanNameFunction']),

            // cryptage
            new \Twig_SimpleFunction('obfuscateLinkJS', [$this, 'obfuscateLinkFunction']),
        ];
    }


    /**
     * Functions
     */


    /**
     * this function cleans up the filename
     *
     * @param string $fileName
     * @access public
     * @return string
     * @static
     *
     * @author Riad Hellal <hellal.riad@gmail.com>
     */
    public function getCleanNameFunction($fileName)
    {
    	$fileName = strtolower($fileName);
    	$string = substr($fileName, 0, strlen($fileName)- 4);
    	$code_entities_match 	= array( '-' ,'_' ,'.');
    	$code_entities_replace 	= array(' ' ,' ' ,' ');
    	$name = str_replace($code_entities_match, $code_entities_replace, $string);

    	return $name;
    }

    /**
     * Creating a link.
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function linkFunction( $label, $path, $options = array() ) {
        $attributes = '';
        foreach ( $options as $key=>$value ) {
            $attributes .= ' ' . $key . '="' . $value . '"';
        }

        return '<a href="' . $path . '"' . $attributes . '>' . $label . '</a>';
    }

    /**
     * Return the image flag of a country.
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getImgFlagByCountryFunction($country, $type ="img", $taille="16")
    {
        $locale                = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
        $all_countries         = $this->container->get('sfynx.tool.string_manager')->allCountries($locale);
        $all_countries_en     = $this->container->get('sfynx.tool.string_manager')->allCountries("en_GB");
        //
        if (isset($all_countries[strtolower($country)])) {
            $img_country  = str_replace(' ', '-', $all_countries_en[strtolower($country)]) . "-Flag-".$taille.".png";
            $name_country = $all_countries[strtolower($country)]; // locale_get_display_name(strtolower($entity->getCountry()), strtolower($locale))
            $src          = $this->container->getParameter('kernel.http_host') . "/bundles/sfynxtemplate/images/flags/png/" . $img_country;
        } else {
            $img_country  = "Default-Flag-".$taille.".png";
            $name_country = $country;
            $src          = $this->container->getParameter('kernel.http_host') . "/bundles/sfynxtemplate/images/flags/default/Default-Flag-".$taille.".png";
        }
        if ($type == "img_counry") {
            return $img_country;
        } elseif ($type == "name_country") {
            return $name_country;
        } elseif ($type == "balise") {
            return "<img src='{$src}' alt='{$name_country} flag' title='{$name_country} flag'/>";
        }
    }

    /**
     * translation of date.
     *
     * @author riad hellal <hellal.riad@gmail.com>
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getDatePatternByLocalFunction($locale)
    {
        $fileName  = $this->container->getParameter("sfynx.tool.date.cache_file");
        $root_file = realpath($fileName);
        if (!$root_file) {
            $isGood = $this->updateCulturesJsFilesFunction($fileName);
            $root_file  = realpath($fileName);
        }
        // we parse the data file of all formats
        $dates  = array();
        $dates  = json_decode(file_get_contents($root_file));
        // we set the locale value
        $locale = strtolower(substr($locale, 0, 2));
        $root_file = realpath($this->container->getParameter("kernel.root_dir") . "/../web/bundles/sfynxtemplate/js/ui/i18n/jquery.ui.datepicker-{$locale}.js");
        if (!$root_file) {
            $locale = "en-GB";
        }
        // we return the locale format of the date
        if (isset($dates->{$locale})) {
            return $dates->{$locale};
        }
        return "dd/MM/yy";  // "MM/dd/yyyy";
    }

    /**
     * parsing translaion js files.
     *
     * @author riad hellal <hellal.riad@gmail.com>
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function updateCulturesJsFilesFunction($fileName = "")
    {
        if (empty($fileName)) {
            $fileName  = $this->container->getParameter("sfynx.tool.date.cache_file");
        }
        $rout_i18n_files = realpath($this->container->getParameter("kernel.root_dir") . "/../web/bundles/sfynxtemplate/js/ui/i18n/");
        $MyDirectory = opendir($rout_i18n_files);
        $fp = fopen($fileName, 'w');
        while($Entry = @readdir($MyDirectory)) {
            if ($Entry != '.' && $Entry != '..') {
                $ch = file_get_contents($rout_i18n_files."/".$Entry, FILE_USE_INCLUDE_PATH);
                preg_match('/dateFormat:([ ]*)\'(.+)\',/', $ch, $match);
                preg_match('#datepicker.regional\[[\'"]{1}(?P<value>(.*))[\'"]{1}\]#sU', $ch, $locale);
                if (isset($locale['value'])) {
                    $ln = $locale['value'];
                } else {
                    print_r($rout_i18n_files."/".$Entry);exit;
                }

                $posts[$ln] =  str_replace('m','M',$match[2]);
            }
        }
        fwrite($fp, json_encode($posts));
        fclose($fp);
        closedir($MyDirectory);

        return true;
    }


    /**
     * divers Filters
     */

    public function statusFilter($entity)
    {
    	if (is_object($entity)) {
            $enabled = $entity->getEnabled();
            $archivedAt = $entity->getArchiveAt();
            $archived = $entity->getArchived();
    	} else {
            $enabled = $entity['enabled'];
            $archivedAt = $entity['archive_at'];
            $archived = $entity['archived'];
    	}
    	if (($enabled  == true ) && ($archived == false)) {
            $status =  $this->container->get('translator')->trans('pi.grid.action.active');
    	} elseif (!empty($archivedAt) && ($archived == true)) {
            $status = $this->container->get('translator')->trans('pi.grid.action.row_archived');
    	} elseif (($enabled  == false ) && ($archived == false)) {
            $status = $this->container->get('translator')->trans('pi.grid.action.activation.waiting');
    	}

    	return $status;
    }

    public function phpFilter($var, $function) {
        return $function($var);
    }

    public function joinphpFilter( $objects, $glue = ', ', $lastGlue = null ) {
        null === $lastGlue && $lastGlue = $glue;
        $last = '';
        if (2 < count($objects)) {
            $last = $lastGlue . array_pop($objects);
        }

        return implode($glue, $objects) . $last;
    }

    public function dumpFilter($var) {
        var_dump($var);
        return '';
    }

    public function print_rFilter($var) {
        return print_r($var, 1);
    }

    public function get_classFilter($object) {
        return get_class($object);
    }

    public function nl2brFilter($string) {
        return nl2br($string);
    }

    public function htmlspecialcharsFilter( $string ) {
        $flags = ENT_COMPAT;
        defined('ENT_HTML5') && $flags |= ENT_HTML5;

        return htmlspecialchars($string, $flags, 'UTF-8');
    }

    public function htmlentitiesFilter( $string ) {
        $flags = ENT_COMPAT;
        defined('ENT_HTML5') && $flags |= ENT_HTML5;

        return htmlentities($string, $flags, 'UTF-8');
    }

    public function addslashesFilter( $string ) {
        return addslashes($string);
    }

    public function substrFilter( $string, $first, $last = null){
        if ((null === $last)) {
            return substr($string, $first);
        }
        return substr($string, $first, $last);
    }

    /**
     * array filters
     */
    public function countFilter($array) {
        return count($array);
    }

    public function resetFilter($array) {
        reset($array);
        return $array;
    }

    public function endFilter($array) {
        end($array);
        return $array;
    }

    public function stepsFilter($array, $step) {
        $count = count($array);
        if ($count >= $step){
            reset($array);
            for ($i=1; $i <= $step; $i++) {
                next($array);
            }
            return current($array);
        }
        return '';
    }

    public function arraysliceFilter($array, $first, $last = null) {
        if ((null === $last)) {
            $result = array_slice($array, $first);
        } else {
            $result = array_slice($array, $first, $last);
        }
        if (count($result) >= 1) {
            return $result;
        }
        return '';
    }

    public function XmlString2arrayFilter($string){
        return $this->container->get('sfynx.tool.array_manager')->XmlString2array($string);
    }

    public function orderByFilter($objs, $orderMethod, $orderBy = "ASC") {
    	if ($objs instanceof \Doctrine\ORM\PersistentCollection) {
            $array = [];
            foreach ($objs as $obj) {
                if (method_exists($obj, $orderMethod)) {
                    $array[$obj->$orderMethod()] = $obj;
                }
                throw ServiceException::serviceNotConfiguredCorrectly();
            }
            if ($orderBy == "ASC") {
                ksort($array);
            } elseif ($orderBy == "DESC") {
                krsort($array);
            }

            return $array;
    	}
        throw ServiceException::serviceNotConfiguredCorrectly();
    }

    public function unsetFilter(array $array, array $unset_keys) {
    	foreach ($unset_keys as $key) {
            unset($array[$key]);
    	}

    	return $array;
    }

    /**
     * crop a picture.
     *
     * <code>
     *   {{ content|renderResponse($params)|raw }}
     * </code>
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function renderResponseFilter($content, $params = [])
    {
        return $this->container->get('twig')->createTemplate($content)->render($params);
    }

    /**
     * translation filters
     */
    public function translatepluralFilter($single, $plural, $number, $domain = "messages")
    {
    	$number = intval($number);
        if ($number > 1) {
            return $this->container->get('translator')->trans(sprintf($plural, $number), ['%s'=>$number], $domain);
        }
        return $this->container->get('translator')->trans(sprintf($single, $number), ['%s'=>$number], $domain);
    }

    public function pluralizeFilter($string, $number = null) {
        if ($number && ($number == 1)) {
            return $string;
        }
        return $this->container->get('sfynx.tool.string_manager')->pluralize($string);
    }

    public function depluralizeFilter($string, $number = null) {
        if ($number && ($number > 1)) {
            return $string;
        }
        return $this->container->get('sfynx.tool.string_manager')->depluralize($string);
    }

    /**
     * text filters
     */
    public function ucfirstFilter($string) {
        return ucfirst($string);
    }

    public function ucwordsFilter($string) {
        return ucwords($string);
    }

    public function cleanWhitespaceFilter($string) {
        return $this->container->get('sfynx.tool.string_manager')->cleanWhitespace($string);
    }

    public function sanitizeFilter($string, $force_lowercase = true, $anal = false, $trunc = 100) {
        return $this->container->get('sfynx.tool.string_manager')->sanitize($string, $force_lowercase, $anal, $trunc);
    }

    public function slugifyFilter($string) {
        return $this->container->get('sfynx.tool.string_manager')->slugify($string);
    }

    public function departementFilter($id) {
        $em = $this->container->get('doctrine')->getManager();
        $departement  = $em->getRepository('M1MProviderBundle:Region')
                ->findOneBy(array('id' => $id));

        return $departement;
    }


    public function limitecaractereFilter($string, $mincara, $nbr_cara) {
        return $this->container->get('sfynx.tool.string_manager')
                ->LimiteCaractere($string, $mincara, $nbr_cara);
    }

    public function splitTextFilter($string){
        return $this->container->get('sfynx.tool.string_manager')
                ->splitText($string);
    }
    public function splitHtmlFilter($string){
        return $this->container->get('sfynx.tool.string_manager')
                ->splitHtml($string);
    }

    public function truncateFilter($string, $length = 100, $ending = "...", $exact = false, $html = true) {
        return $this->container->get('sfynx.tool.string_manager')
                ->truncate($string, $length, $ending, $exact, $html);
    }

    public function cutTextFilter($string, $intCesurePos, $otherText = false, $strCaractereCesure = ' ', $intDecrementationCesurePos = 5){
        $HtmlCutter    = $this->container->get('sfynx.tool.string_cut_manager');
        $HtmlCutter->setOptions($string, $intCesurePos, $otherText);
        $HtmlCutter->setParams($strCaractereCesure, $intDecrementationCesurePos);

        return $HtmlCutter->run();
    }

    /**
     * encrypt string
     *
     * @param string $string
     * @param string $key
     */
    public function encryptFilter($string, $key = "0A1TG4GO")
    {
        $encryption    = $this->container->get('sfynx.tool.encryption_manager');

        return $encryption->encryptFilter($string, $key);
    }

    /**
     * decrypt string
     *
     * @param string $string
     * @param string $key
     */
    public function decryptFilter($string, $key = "0A1TG4GO")
    {
        $encryption    = $this->container->get('sfynx.tool.encryption_manager');

        return $encryption->decryptFilter($string, $key);
    }

    /**
     * Obfuscate link. SEO worst practice.
     *
     * @param string $url
     */
    public function obfuscateLinkFilter($url, $base16 = "0A12B34C56D78E9F")
    {
        $encryption    = $this->container->get('sfynx.tool.encryption_manager');

        return $encryption->obfuscateLinkEncrypt($url, $base16);
    }

    /**
     * Obfuscate link JS. SEO worst practice.
     *
     * @param string $fileName
     * @access public
     * @return string
     * @static
     *
     */
    public function obfuscateLinkFunction($balise = "a", $class = "hiddenLink", $base16 = "0A12B34C56D78E9F")
    {
    	$encryption    = $this->container->get('sfynx.tool.encryption_manager');

        return $encryption->obfuscateLinkDecrypt($balise, $class, $base16);
    }
}
