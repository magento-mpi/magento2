<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\ParserLexer;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Scalar_LNumber;

class IntegerNumberReference extends AbstractScalarReference
{
    /**
     * This method constructs a new reference based on the specified integer number.
     * @param PHPParser_Node_Scalar_LNumber $node
     */
    public function __construct(PHPParser_Node_Scalar_LNumber $node)
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
        // if the original value exists, just use that so that the number representation does not change
        $stringValue = $this->node->getAttribute(ParserLexer::ORIGINAL_VALUE);
        if (!isset($stringValue)) {
            // otherwise, do the best guess at resolving it as a number
            $stringValue = (string)$this->node->value;
        }
        // add the value to the end of the current line
        $this->addToLine($treeNode, $stringValue);
        return $treeNode;
    }
}
