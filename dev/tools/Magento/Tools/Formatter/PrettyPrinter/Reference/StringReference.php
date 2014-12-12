<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\ParserLexer;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Scalar_String;

class StringReference extends AbstractScalarReference
{
    /**
     * This method constructs a new statement based on the specified string
     * @param PHPParser_Node_Scalar_String $node
     */
    public function __construct(PHPParser_Node_Scalar_String $node)
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
        $heredocCloseTag = $this->node->getAttribute(ParserLexer::HEREDOC_CLOSE_TAG);
        // resolve the string based on the details found in the node
        if (null !== $heredocCloseTag) {
            // heredoc was specified, so check for original string to build up the value
            if (null !== $stringValue) {
                $this->processHeredoc($treeNode, $heredocCloseTag, $stringValue);
            } else {
                $this->processHeredoc($treeNode, $heredocCloseTag, $this->node->value);
            }
        } elseif (null !== $stringValue) {
            // original string detected, so use it
            $this->addToLine($treeNode, $stringValue);
        } else {
            // if nothing there, then use the parsed data
            $this->addToLine($treeNode, '\'')->add(addcslashes($this->node->value, '\'\\'))->add('\'');
        }
        return $treeNode;
    }
}
