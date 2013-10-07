<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use PHPParser_Parser;
use Magento\Tools\Formatter\ParserLexer;

/**
 * This class is used to control pretty printing of a block of code.
 * Class Printer
 * @package Magento\Tools\Formatter\PrettyPrinter
 */
class Printer {
    /**
     * @var string
     */
    protected $originalCode;

    /**
     * @var string
     */
    protected $formattedCode;

    /**
     * This method is used to construct the printer for the given code block.
     */
    public function __construct($code) {
        // save the original code
        $this->originalCode = $code;
        // allocate the parser--should probably be done statically
        $parser = new PHPParser_Parser(new ParserLexer());
        // parse the code into statements
        $statements = $parser->parse($this->originalCode);
        // resolve the code into its final version
        $tree = new Tree();
        $this->formattedCode = $this->processStatements($statements, $tree);
    }

    /**
     * This method returns the code as a fomatted block.
     */
    public function getFormattedCode() {
        return $this->originalCode;
    }

    /**
     * This method looks at the group of statements and process them as an array or as an individual statement.
     * @param $statements
     * @param Tree $tree Tree representation of the resulting code
     */
    protected function processStatements($statements, Tree $tree) {
        // if it is an array, process each element in the array
        if (is_array($statements)) {
            foreach($statements as $node) {
                $this->processStatement($node, $tree);
            }
        }
        else {
            // otherwise, it just a single statement
            $this->processStatement($statements, $tree);
        }
    }

    /**
     * This method parses the given statement.
     * @param \PHPParser_NodeAbstract $node
     * @param Tree $tree Tree representation of the resulting code
     */
    protected function processStatement(\PHPParser_NodeAbstract $node, Tree $tree) {
        $statement = StatementFactory::getInstance()->getStatement($node);
        $statement->process($tree);
    }
}