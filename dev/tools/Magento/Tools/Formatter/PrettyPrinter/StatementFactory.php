<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * This class controls the mapping of the parser nodes to printer nodes.
 */
class StatementFactory {
    /**
     * This member holds the singleton instance of this class.
     *
     * @var StatementFactory
     */
    private static $instance = null;

    /**
     * This member holds the mapping of parser nodes to statement classes.
     * @var array
     */
    protected $nodeMap = array();

    /**
     * This method returns an instance of a statement class used to process the given node.
     *
     * @param \PHPParser_Node $parserNode
     */
    public function getStatement(\PHPParser_Node $parserNode) {
        // assume the type is not recognized
        $statementName = UnknownStatement::getType();
        // if the the type is a registered type, return that class instead
        if (array_key_exists($parserNode->getType(), $this->nodeMap)) {
            $statementName = $this->nodeMap[$parserNode->getType()];
        }
        // return an instance of the class with the parsed node as a parameter
        return new $statementName($parserNode);
    }

    /**
     * This method constructs the new factory. By default, it registers the known statement types.
     */
    protected function __construct() {
        $this->register('Stmt_Class', ClassStatement::getType());
        $this->register('Stmt_InlineHTML', InlineHtmlStatement::getType());
    }

    /**
     * This method registers the given parser node type to the named statement.
     *
     * @param string $parserNodeName Contains the name corresponding to the type of parser node
     * @param string $statement Contains the name of the class used to process the parser node
     */
    protected function register($parserNodeName, $statement) {
        $this->nodeMap[$parserNodeName] = $statement;
    }

    /**
     * This method returns the singleton instance of the factory.
     *
     * @return StatementFactory
     */
    static public function getInstance() {
        if (null === self::$instance) {
            self::$instance = new StatementFactory();
        }

        return self::$instance;
    }
}