<?php
namespace Sfynx\ToolBundle\Twig;

use Twig\Loader\ExistsLoaderInterface;
use Twig\Loader\LoaderInterface;
use Twig\Loader\SourceContextLoaderInterface;
use Twig\Source;

/**
 * Class StringLoader
 *
 * @subpackage   Admin_Twig
 * @package    Loader
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2017-01-03
 */
class PiStringLoader implements LoaderInterface, SourceContextLoaderInterface, ExistsLoaderInterface
{
    /**
     * @inheritdoc
     */
    public function getSource($name)
    {
        return $name;
    }

    /**
     * @inheritDoc
     */
    public function getSourceContext($name)
    {
        return new Source($name, $name);
    }

    /**
     * @inheritdoc
     */
    public function getCacheKey($name)
    {
        return $name;
    }

    /**
     * @inheritdoc
     */
    public function isFresh($name, $time)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function exists($name)
    {
        return preg_match('/\s/', $name);
    }
}