<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\ConditionalLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node;

/**
 * This class is used as the base class for all types of lines and partial lines (e.g. statements and references).
 * Class BaseAbstract
 * @package Magento\Tools\Formatter\PrettyPrinter\Statement
 */
abstract class BaseAbstract
{
    /**
     * This member holds the current node.
     * @var PHPParser_Node
     */
    protected $node;

    /**
     * This method constructs a new statement based on the specify node.
     * @param PHPParser_Node $node
     */
    protected function __construct(PHPParser_Node $node)
    {
        $this->node = $node;
    }

    /**
     * This method returns the full name of the class.
     *
     * @return string Full name of the class is called through.
     */
    public static function getType()
    {
        return get_called_class();
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public abstract function resolve(TreeNode $treeNode);

    /**
     * This method adds the arguments to the current line
     * @param array $arguments
     * @param TreeNode $treeNode
     * @param Line $line
     * @param bool $initialBreakRequired Optional flag indicating to include the first break.
     */
    protected function processArgumentList(
        array $arguments,
        TreeNode $treeNode,
        Line $line,
        $initalBreakRequired = true
    )
    {
        foreach ($arguments as $index => $argument) {
            if ($index == 0 && $initalBreakRequired || $index > 0) {
                $line->add(new ConditionalLineBreak(' '));
            }
            // process the argument itself
            $this->resolveNode($argument, $treeNode);
            // in not the last on, separate with a comma
            if ($index < sizeof($arguments) - 1) {
                $line->add(',');
            }
        }
    }

    /**
     * This method resolves the node immediately.
     * @param PHPParser_Node $node
     * @param TreeNode $treeNode TreeNode representing the current node.
     */
    protected function resolveNode(PHPParser_Node $node, TreeNode $treeNode)
    {
        $statement = StatementFactory::getInstance()->getStatement($node);
        $statement->resolve($treeNode);
    }
}