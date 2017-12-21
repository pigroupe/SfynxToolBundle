<?php
/**
 * This file is part of the <Tool> project.
 * 
 * @subpackage   Script
 * @package    Extension
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-11
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\ToolBundle\Twig\Node;

/**
 * Javascripts Node.
 *
 * @subpackage   Script
 * @package    Extension
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class JavascriptsNode extends \Twig_Node
{
    /**
     * @param     \Twig_NodeInterface     $value
     * @param     \Twig_Node_Expression     $order
     * @param     integer                 $lineno
     * @param     string                     $tag (optional)
     * @return     void
     */
    public function __construct($extensionName, \Twig_Node $value, $lineno, $tag = null)
    {
        $this->extensionName = $extensionName;
        
        //parent::__construct(array(), array(), $lineno, $tag);
        parent::__construct(['value' => $value], [], $lineno, $tag);
    }

    /**
     * @param \Twig_Compiler $compiler
     * @return void
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler
            ->write(sprintf("echo \$this->env->getExtension('%s')->renderScript(", $this->extensionName))
            ->subcompile($this->getNode('value'))
            //->raw(', ')
            //->subcompile($this->getNode('order'))
            ->raw(");\n");
    }
}
