<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Scalar;

/**
 * This class will return the string passed in.
 * Class ScalarReference
 * @package Magento\Tools\Formatter\PrettyPrinter\Reference
 */
class AbstractScalarReference extends AbstractReference
{
    protected $result;

    /**
     * This method constructs a new statement based on the specify class node
     * @param PHPParser_Node_Scalar $node
     * @param mixed $result Optional value to return in resolve.
     */
    public function __construct(PHPParser_Node_Scalar $node, $result = null)
    {
        parent::__construct($node);
        $this->result = $result;
    }

    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     *
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // optionally add in the result
        if (null !== $this->result) {
            /** @var Line $line */
            $line = $treeNode->getData()->line;
            // add in the constant value
            $line->add($this->result);
        }
    }
}