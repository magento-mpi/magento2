<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\ParserLexer;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Scalar_Encapsed;

class EncapsedReference extends AbstractScalarReference
{
    const ENCAPSED_QUOTE = '"';

    /**
     * This method constructs a new statement based on the specified string
     * @param PHPParser_Node_Scalar_Encapsed $node
     */
    public function __construct(PHPParser_Node_Scalar_Encapsed $node)
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
        // need to deal with heredoc
        $heredocCloseTag = $this->node->getAttribute(ParserLexer::HEREDOC_CLOSE_TAG);
        if (null !== $heredocCloseTag) {
            if ($this->node->hasAttribute(ParserLexer::ORIGINAL_VALUE)) {
                $this->processHeredoc(
                    $treeNode,
                    $heredocCloseTag,
                    $this->node->getAttribute(ParserLexer::ORIGINAL_VALUE)
                );
            } else {
                $this->processHeredoc($treeNode, $heredocCloseTag, $this->node->parts);
            }
        } else {
            $this->addToLine($treeNode, self::ENCAPSED_QUOTE);
            $this->encapsList($this->node->parts, self::ENCAPSED_QUOTE, $treeNode);
            $this->addToLine($treeNode, self::ENCAPSED_QUOTE);
        }
        return $treeNode;
    }
}
