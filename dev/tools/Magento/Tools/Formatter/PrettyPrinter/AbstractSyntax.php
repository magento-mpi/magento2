<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Comment;
use PHPParser_Node;

/**
 * This class is used as the base class for all types of lines and partial lines (e.g. statements and references).
 * Class BaseAbstract
 */
abstract class AbstractSyntax
{
    /**
     * Key into node attributes for comments
     */
    const ATTRIBUTE_COMMENTS = 'comments';

    /**
     * This member holds the current node.
     *
     * @var PHPParser_Node
     */
    protected $node;

    /**
     * This method constructs a new statement based on the specified node.
     *
     * @param PHPParser_Node $node
     */
    protected function __construct(PHPParser_Node $node)
    {
        $this->node = $node;
    }

    /**
     * This method adds the comments associated with the syntax node to the given tree node.
     *
     * @param TreeNode $treeNode TreeNode representing the current node.
     * @return void
     */
    public function addComments(TreeNode $treeNode)
    {
        // get the comments to add
        $comments = $this->getComments();
        // only attempt to add comments if they are present
        if (isset($comments) && is_array($comments)) {
            // add individual lines of the comments to the tree
            foreach ($comments as $comment) {
                // Remove comment from map since it is being consumed
                if ($comment instanceof PHPParser_Comment) {
                    unset(Printer::$lexer->commentMap[$comment->getLine()]);
                }
                // split the lines so that they can be indented correctly
                $commentLines = explode(HardLineBreak::EOL, $comment->getReformattedText());
                foreach ($commentLines as $commentLine) {
                    // add the line individually to the tree so that they can be indented correctly
                    $this->addCommentToNode($commentLine, $treeNode);
                }
            }
        }
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     *
     * @param TreeNode $treeNode TreeNode representing the current node.
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
     * This method adds the current comment line to the current tree node.
     *
     * @param string $commentLine String containing the current comment.
     * @param TreeNode $treeNode TreeNode representing the current node.
     * @return void
     */
    protected function addCommentToNode($commentLine, TreeNode $treeNode)
    {
        // default action is to add the comment as a prior sibling to the current node
        $newNode = AbstractSyntax::getNodeLine((new Line($commentLine))->add(new HardLineBreak()));
        $treeNode->addSibling($newNode, false);
    }

    /**
     * This method adds the token to the current line in the tree node.
     *
     * @param TreeNode $treeNode TreeNode representing the current node.
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
     * This method returns if the needle can be found at the end of the haystack.
     *
     * @param string $haystack String to look in.
     * @param string $needle String to find.
     * @param bool $caseInsensitivity If true, then comparison is case insensitive.
     * @return bool
     */
    protected function endsWith($haystack, $needle, $caseInsensitivity = false)
    {
        $found = false;
        // determine lengths to make sure the haystack is longer than the needle
        $haystackLength = strlen($haystack);
        $needleLength = strlen($needle);
        // only need to do the compare if the haystack can actually contain the needle
        if ($haystackLength >= $needleLength) {
            $found = substr_compare($haystack, $needle, -$needleLength, $needleLength, $caseInsensitivity) === 0;
        }
        return $found;
    }

    /**
     * Return the array that contains the comments from the node's attributes, if it is there.
     *
     * @return null|array
     */
    protected function getComments()
    {
        $comments = null;
        if ($this->node->hasAttribute(self::ATTRIBUTE_COMMENTS)) {
            $comments = $this->node->getAttribute(self::ATTRIBUTE_COMMENTS);
            if (isset($comments) && $this->isTrimComments()) {
                $this->trimComments($comments);
            }
        }
        return $comments;
    }

    /**
     * This method processes the argument list as a parenthesis wrapped argument list.
     *
     * @param array $arguments Array of arguments to process.
     * @param TreeNode $treeNode TreeNode representing the current node.
     * @param LineBreak $lineBreak Class used to inject between arguments as a separator.
     * @return TreeNode
     */
    protected function processArgsList(array $arguments, TreeNode $treeNode, LineBreak $lineBreak)
    {
        // search for a closure as one of the arguments
        $closure = new ClosureDetection($arguments);
        if ($closure->hasClosure()) {
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
     *
     * @param array $arguments Array of arguments to process.
     * @param TreeNode $treeNode TreeNode representing the current node.
     * @param LineBreak $lineBreak Class used to inject between arguments as a separator.
     * @return TreeNode
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
     *
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
     *
     * @param TreeNode|TreeNode[] $nodes Array or single node
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
     *
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
     * This method will modify the array that is passed in to remove blank lines in the first or last position of the
     * array.
     *
     * @param string[] &$comments
     * @return void
     */
    protected function trimComments(&$comments)
    {
        // reset and end will short circuit the loops when the array is empty
        // Trim blank lines before the comment
        while (reset($comments) && preg_match('/^\s*\n$/', reset($comments))) {
            array_shift($comments);
        }
        // Trim blank lines at the end of the comment
        while (end($comments) && preg_match('/^\s*\n$/', end($comments))) {
            array_pop($comments);
        }
    }

    /**
     * This method is a help method used to return a new node.
     *
     * @param AbstractSyntax $syntax
     * @param Line|null $line
     * @return TreeNode
     */
    public static function getNode(AbstractSyntax $syntax, $line = null)
    {
        return new TreeNode(new LineData($syntax, $line));
    }

    /**
     * This method is a help method used to return a new node.
     *
     * @param Line $line
     * @return TreeNode
     */
    public static function getNodeLine(Line $line)
    {
        return new TreeNode(new LineData(null, $line));
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
     * Method to let us know if we should trim blank lines before and after comments on this syntax element.
     *
     * @return bool
     */
    public function isTrimComments()
    {
        return false;
    }
}
