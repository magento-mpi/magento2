<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\PrettyPrinter\LineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node;

/**
 * This class is used as the base class for all types of lines and partial lines (e.g. statements and references).
 * Class BaseAbstract
 * @package Magento\Tools\Formatter\PrettyPrinter
 */
abstract class AbstractSyntax
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
    public function resolve(TreeNode $treeNode) {
        /** @var LineData $lineData */
        $lineData = $treeNode->getData();
        // if the line has not been resolved, start with a blank line
        if (null === $lineData->line) {
            $lineData->line = new Line();
        }
    }

    /**
     * This method adds the arguments to the current line
     * @param array $arguments
     * @param TreeNode $treeNode
     * @param Line $line
     * @param LineBreak $lineBreak
     */
    protected function processArgumentList(
        array $arguments,
        TreeNode $treeNode,
        Line $line,
        LineBreak $lineBreak
    ) {
        if (!empty($arguments)) {
            foreach ($arguments as $index => $argument) {
                // add the line break prior to the argument
                $line->add($lineBreak);
                // process the argument itself
                $this->resolveNode($argument, $treeNode);
                // if not the last one, separate with a comma
                if ($index < sizeof($arguments) - 1) {
                    $line->add(',');
                }
            }
            if ($lineBreak->isAfterListRequired()) {
                $line->add($lineBreak);
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
        /** @var AbstractSyntax $statement */
        $statement = SyntaxFactory::getInstance()->getStatement($node);
        $statement->resolve($treeNode);
    }

    /**
     * This method is a help method used to return a new node.
     */
    public static function getNode(AbstractSyntax $syntax, $line = null) {
        return new TreeNode(new LineData($syntax, $line));
    }

    /**
     * This method is a help method used to return a new node.
     */
    public static function getNodeLine(Line $line) {
        return new TreeNode(new LineData(null, $line));
    }
}