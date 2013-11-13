<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
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
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // if the original value exists, just use that so that the number representation does not change
        $stringValue = $this->node->getAttribute(ParserLexer::ORIGINAL_VALUE);
        $heredocCloseTag = $this->node->getAttribute(ParserLexer::HEREDOC_CLOSE_TAG);
        if (null !== $heredocCloseTag) {
            $this->processHeredoc($treeNode, $heredocCloseTag, array($this->node->value));
        } elseif (null === $stringValue) {
            // if nothing there, then use the raw data
            $this->addToLine($treeNode, '\'')->add(addcslashes($this->node->value, '\'\\'))->add('\'');
        } else {
            $this->addToLine($treeNode, $stringValue);
        }
        return $treeNode;
    }
}
