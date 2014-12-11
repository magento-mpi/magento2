<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Statement;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Stmt_InlineHTML;

class InlineHtmlStatement extends AbstractStatement
{
    /**
     * This method constructs a new statement based on the specified statement.
     * @param PHPParser_Node_Stmt_InlineHTML $node
     */
    public function __construct(PHPParser_Node_Stmt_InlineHTML $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // assume in the context of php, so close it
        $this->addToLine($treeNode, '?>')->add(new HardLineBreak());
        // remove the last trailing whitespace because the loop will add it back
        $body = rtrim($this->node->value);
        $prefix = null;
        $prefixLength = null;
        // split the value to add lines such that they are indented with the PHP source
        $lines = explode(HardLineBreak::EOL, $body);
        foreach ($lines as $line) {
            if (null === $prefixLength) {
                $trimmedLine = ltrim($line);
                $prefixLength = strlen($line) - strlen($trimmedLine);
                if ($prefixLength > 0) {
                    $prefix = substr($line, 0, $prefixLength);
                }
            }
            if ($prefixLength > 0 && strpos($line, $prefix) === 0) {
                $line = substr($line, $prefixLength);
            }
            // print the HTML
            $this->addToLine($treeNode, $line)->add(new HardLineBreak());
        }
        // go back to PHP
        $this->addToLine($treeNode, '<?php')->add(new HardLineBreak());
        return $treeNode;
    }
}
