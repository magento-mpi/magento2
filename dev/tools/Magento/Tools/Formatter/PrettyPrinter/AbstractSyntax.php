<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node;
use PHPParser_Node_Arg;
use PHPParser_Node_Expr_ArrayItem;
use PHPParser_Node_Expr_Closure;
use PHPParser_Node_Expr_FuncCall;

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
     * This method constructs a new statement based on the specified node.
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
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        /** @var LineData $lineData */
        $lineData = $treeNode->getData();
        // if the line has not been resolved, start with a blank line
        if (null === $lineData->line) {
            $lineData->line = new Line();
        }
        return $treeNode;
    }

    /**
     * This method adds the token to the current line in the tree node.
     * @param TreeNode $treeNode Node containing the current statement.
     * @param mixed $token Token to be added to the line.
     * @return Line that was just added to.
     */
    protected function addToLine(TreeNode $treeNode, $token)
    {
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // add the message to the line
        return $line->add($token);
    }

    /**
     * This method searches for a closure node in the arguments.
     * @param array $arguments Array of arguments to process.
     */
    protected function hasClosure(array $arguments)
    {
        $closure = false;
        // only need to look if something was specified
        if (!empty($arguments)) {
            foreach ($arguments as $argument) {
                if ($argument instanceof PHPParser_Node_Arg &&
                    $argument->value instanceof PHPParser_Node_Expr_Closure ||
                    $argument instanceof PHPParser_Node_Expr_ArrayItem &&
                        $argument->value instanceof PHPParser_Node_Expr_Closure
                ) {
                    $closure = true;
                    break;
                } elseif ($argument instanceof PHPParser_Node_Arg &&
                    $argument->value instanceof PHPParser_Node_Expr_FuncCall
                ) {
                    $closure = $this->hasClosure($argument->value->args);
                    if ($closure === true) {
                        break;
                    }
                }
            }
        }
        return $closure;
    }

    /**
     * This method processes the argument list as a parenthesis wrapped argument list.
     * @param array $arguments Array of arguments to process.
     * @param TreeNode $treeNode TreeNode representing the current node.
     * @param LineBreak $lineBreak Class used to inject between arguments as a separator.
     * @return TreeNode
     */
    protected function processArgsList(array $arguments, TreeNode $treeNode, LineBreak $lineBreak)
    {
        // search for a closure as one of the arguments
        if ($this->hasClosure($arguments)) {
            // force the multi-line argument list
            $this->addToLine($treeNode, '(')->add(new HardLineBreak());
            foreach ($arguments as $index => $argument) {
                // create a new child for each argument
                $line = new Line();
                $lastProcessedNode = $treeNode->addChild(AbstractSyntax::getNodeLine($line));
                // process the argument itself
                $lastProcessedNode = $this->resolveNode($argument, $lastProcessedNode);
                // if not the last one, separate with a comma
                if ($index < sizeof($arguments) - 1) {
                    $this->addToLine($lastProcessedNode, ',');
                }
                // each argument will have a hard line break
                $this->addToLine($lastProcessedNode, new HardLineBreak());
            }
            // add the closing on a new line
            $treeNode = $treeNode->addSibling(AbstractSyntax::getNodeLine(new Line(')')));
        } else {
            // just process as normal
            $this->addToLine($treeNode, '(');
            $treeNode = $this->processArgumentList($arguments, $treeNode, $lineBreak);
            $this->addToLine($treeNode, ')');
        }
        return $treeNode;
    }

    /**
     * This method adds the arguments to the current line
     * @param array $arguments Array of arguments to process.
     * @param TreeNode $treeNode TreeNode representing the current node.
     * @param LineBreak $lineBreak Class used to inject between arguments as a separator.
     */
    protected function processArgumentList(array $arguments, TreeNode $treeNode, LineBreak $lineBreak)
    {
        if (!empty($arguments)) {
            foreach ($arguments as $index => $argument) {
                // add the line break prior to the argument
                $this->addToLine($treeNode, $lineBreak);
                // If $argument is null there is nothing to resolve
                if ($argument !== null) {
                    // process the argument itself
                    $treeNode = $this->resolveNode($argument, $treeNode);
                }
                // if not the last one, separate with a comma
                if ($index < sizeof($arguments) - 1) {
                    $this->addToLine($treeNode, ',');
                }
            }
            if ($lineBreak->isAfterListRequired()) {
                $this->addToLine($treeNode, $lineBreak);
            }
        }
        return $treeNode;
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
     * @return TreeNode
     */
    protected function resolveNode(PHPParser_Node $node, TreeNode $treeNode)
    {
        /** @var AbstractSyntax $statement */
        $statement = SyntaxFactory::getInstance()->getStatement($node);
        return $statement->resolve($treeNode);
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
        $comments = null;
        if ($this->node->hasAttribute(self::ATTRIBUTE_COMMENTS)) {
            $comments = $this->node->getAttribute(self::ATTRIBUTE_COMMENTS);
        }
        return $comments;
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
