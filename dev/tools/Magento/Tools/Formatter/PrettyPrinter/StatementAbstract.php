<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * This class is the base class for all printer statements.
 */
abstract class StatementAbstract implements Node {
    /**
     * This member holds the current node.
     * @var \PHPParser_NodeAbstract
     */
    protected $node;

    /**
     * This method constructs a new statement based on the specify node.
     * @param \PHPParser_NodeAbstract $node
     */
    public function __construct(\PHPParser_NodeAbstract $node) {
        $this->node = $node;
    }

    /**
     * This method returns the full name of the class.
     *
     * @return string Full name of the class is called through.
     */
    public static function getType() {
        return get_called_class();
    }
}