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
     * Key into node attributes for comments
     */
    const ATTRIBUTE_COMMENTS = 'comments';

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
    public function resolve(TreeNode $treeNode)
    {
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
                // If $argument is null there is nothing to resolve
                if ($argument !== null) {
                    // process the argument itself
                    $this->resolveNode($argument, $treeNode);
                }
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
     * This method processes the newly added node.
     * @param TreeNode $originatingNode Node where new nodes are originating from
     * @param TreeNode $newNode Newly added node containing the statement
     * @param int $index 0 based index of the new node
     * @param int $total total number of nodes to be added
     * @param mixed $data Data that is passed to derived class when processing the node.
     * @return \Magento\Tools\Formatter\Tree\TreeNode
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function processNode(TreeNode $originatingNode, TreeNode $newNode, $index, $total, $data = null)
    {
        // default is to add the new node as a child of the originating node
        $originatingNode->addChild($newNode);
        // always return the originating node
        return $originatingNode;
    }

    /**
     * This method parses the given nodes and places them in the tree by calling processNode. This
     * allows the derived class a chance to insert the new node into the appropriate location.
     * @param mixed $nodes Array or single node
     * @param TreeNode $originatingNode Node where new nodes are originating from
     * @param mixed $data Data that is passed to derived class when processing the node.
     * @return TreeNode
     */
    protected function processNodes($nodes, TreeNode $originatingNode, $data = null)
    {
        if (is_array($nodes)) {
            $total = count($nodes);
            foreach ($nodes as $index => $node) {
                $treeNode = AbstractSyntax::getNode(SyntaxFactory::getInstance()->getStatement($node));
                $originatingNode = $this->processNode($originatingNode, $treeNode, $index, $total, $data);
            }
        } else {
            $treeNode = AbstractSyntax::getNode(SyntaxFactory::getInstance()->getStatement($nodes));
            $originatingNode = $this->processNode($originatingNode, $treeNode, 0, 1, $data);
        }
        // return the last node that was added (or whatever was returned from the last node processing)
        return $originatingNode;
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
    public static function getNode(AbstractSyntax $syntax, $line = null)
    {
        return new TreeNode(new LineData($syntax, $line));
    }

    /**
     * This method is a help method used to return a new node.
     */
    public static function getNodeLine(Line $line)
    {
        return new TreeNode(new LineData(null, $line));
    }

    /**
     * Return the array that contains the comments from the node's attributes, if it is there.
     *
     * @return mixed
     */
    public function getComments()
    {
        if ($this->node->hasAttribute(self::ATTRIBUTE_COMMENTS)) {
            return $this->node->getAttribute(self::ATTRIBUTE_COMMENTS);
        }
    }

    /**
     * Remove the comments attribute data if it is there.
     */
    public function removeComments()
    {
        if ($this->node->hasAttribute(self::ATTRIBUTE_COMMENTS)) {
            $this->node->setAttribute(self::ATTRIBUTE_COMMENTS, null);
        }
    }
    /**
     * Method to let us know if we should trim blank lines before and after comments on this syntax element.
     *
     * @return bool
     */
    public function isTrimComments()
    {
        return false;
    }
}
