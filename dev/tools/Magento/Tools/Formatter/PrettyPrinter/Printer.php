<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use PHPParser_Node;
use PHPParser_Parser;
use Magento\Tools\Formatter\ParserLexer;
use Magento\Tools\Formatter\PrettyPrinter\Statement\StatementFactory;
use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;

/**
 * This class is used to control pretty printing of a block of code.
 * Class Printer
 * @package Magento\Tools\Formatter\PrettyPrinter
 */
class Printer
{
    /**
     * @var string
     */
    protected $formattedCode;

    /**
     * @var string
     */
    protected $originalCode;

    /**
     * This method is used to construct the printer for the given code block.
     */
    public function __construct($code)
    {
        // save the original code
        $this->originalCode = $code;
        // allocate the parser--should probably be done statically
        $parser = new PHPParser_Parser(new ParserLexer());
        // parse the code into statements
        $statements = $parser->parse($this->originalCode);
        // convert the statements to text
        $this->resolveStatements($statements);
    }

    /**
     * This method returns the code as a formatted block.
     */
    public function getFormattedCode()
    {
        return $this->formattedCode;
    }

    /**
     * This method adds a new sibling for the passed in node.
     * @param PHPParser_Node $node Node obtained from the parser.
     * @param TreeNode $treeNode Tree node location where the object is going to be added.
     * @return TreeNode Newly added node.
     */
    protected function addRootForNode(PHPParser_Node $node, TreeNode $treeNode)
    {
        $statement = StatementFactory::getInstance()->getStatement($node);
        return $treeNode->addSibling(new TreeNode($statement));
    }

    /**
     * This method resolves the statements into lines.
     * @param mixed $statements PHP Parser nodes to resolve
     */
    protected function resolveStatements($statements)
    {
        // create a new tree, presuming that it is php
        $tree = new Tree();
        $treeNode = $tree->addRoot(new TreeNode((new Line('<?php'))->add(new HardLineBreak())));
        // add in the root nodes
        if (is_array($statements)) {
            foreach ($statements as $node) {
                $treeNode = $this->addRootForNode($node, $treeNode);
            }
        } else {
            $this->addRootForNode($statements, $treeNode);
        }
        // loop through the tree, resolving nodes until there are no more nodes to resolve
        $visitor = new LineResolver();
        do {
            // reset for th next run of the resolved
            $visitor->statementCount = 0;
            // visit all of the nodes, resolving each node to a line
            $tree->traverse($visitor);
        } while ($visitor->statementCount > 0);
        // format the processed tree
        $this->formatTree($tree);
    }

    /**
     * This method takes the statement tree and levels it (i.e. makes sure lines are at the correct
     * level) and prints the tree.
     * @param Tree $tree Tree to operate upon.
     */
    protected function formatTree(Tree $tree)
    {
        // level the nodes to even out the lines
        $visitor = new NodeLeveler();
        $tree->traverse($visitor);
        // print out the nodes
        $visitor = new NodePrinter();
        $tree->traverse($visitor);
        $this->formattedCode = $visitor->result;
    }
}
