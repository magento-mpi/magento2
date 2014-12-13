<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\ParserLexer;
use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node;
use PHPParser_Parser;

/**
 * This class is used to control pretty printing of a block of code.
 * Class Printer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var ParserLexer
     */
    public static $lexer;

    /**
     * This method returns the lexer data member
     *
     * @return array|\Magento\Tools\Formatter\ParserLexer
     */
    public static function getLexer()
    {
        return self::$lexer;
    }

    /**
     * This method is used to construct the printer for the given code block.
     *
     * @param string $code
     */
    public function __construct($code)
    {
        // save the original code
        $this->originalCode = $code;
        // allocate the parser--should probably be done statically
        self::$lexer = new ParserLexer();
    }

    /**
     * This method display the remaining items in the comment map.
     * Any items left in this collection were likely removed by the
     * formatter.
     *
     * @param array $commentMap
     * @return void
     */
    public function displayRemovedComments($commentMap)
    {
        if (isset($commentMap)) {
            if (count($commentMap) > 0) {
                echo "REMOVED COMMENTS" . PHP_EOL;
                while (list($key, $value) = each($commentMap)) {
                    echo "line({$key}): {$value}" . PHP_EOL;
                }
            }
        }
    }

    /**
     * This method returns the code as a formatted block.
     *
     * @return string
     */
    public function getFormattedCode()
    {
        return $this->formattedCode;
    }

    /**
     * This method returns if the formatted code indicates a change.
     *
     * @return bool
     */
    public function hasChange()
    {
        return null !== $this->formattedCode && strcmp($this->originalCode, $this->formattedCode) !== 0;
    }

    /**
     * This method performs the parsing and printing of the original code.
     *
     * @return void
     */
    public function parseCode()
    {
        $parser = new PHPParser_Parser(self::$lexer);
        // parse the code into statements
        $statements = $parser->parse($this->originalCode);
        // convert the statements to text
        $this->resolveStatements($statements);
        // Show comments that were not consumed(output) by the formatting process
        //$this->displayRemovedComments(self::$lexer->getCommentMap());
        // parse the resulting code to verify successful printing
        $parser->parse($this->formattedCode);
    }

    /**
     * This method adds a new sibling for the passed in node.
     *
     * @param PHPParser_Node $node Node obtained from the parser.
     * @param TreeNode $treeNode Tree node location where the object is going to be added.
     * @return TreeNode Newly added node.
     */
    protected function addRootForNode(PHPParser_Node $node, TreeNode $treeNode)
    {
        $statement = SyntaxFactory::getInstance()->getStatement($node);
        return $treeNode->addSibling(AbstractSyntax::getNode($statement));
    }

    /**
     * This method resolves the statements into lines.
     *
     * @param mixed $statements PHP Parser nodes to resolve
     * @return void
     */
    protected function resolveStatements($statements)
    {
        // create a new tree, presuming that it is php
        $tree = new Tree();
        $treeNode = $tree->addRoot(AbstractSyntax::getNodeLine((new Line('<?php'))->add(new HardLineBreak())));
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
     *
     * @param Tree $tree Tree to operate upon.
     * @return void
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
