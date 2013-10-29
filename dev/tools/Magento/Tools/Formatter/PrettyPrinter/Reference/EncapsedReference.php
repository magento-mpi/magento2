<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\ParserLexer;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Scalar_Encapsed;

class EncapsedReference extends AbstractScalarReference
{
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
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        $line->add('"');
        $this->encapsList($this->node->parts, '"', $treeNode);
        $line->add('"');
    }
}
