<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Param;

class ParameterReference extends AbstractVariableReference
{
    /**
     * This method constructs a new statement based on the specified parameter.
     * @param PHPParser_Node_Param $node
     */
    public function __construct(PHPParser_Node_Param $node)
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
        // if the type is specified, add it to the line
        if ($this->node->type) {
            // if the type is a string, just add it
            if (is_string($this->node->type)) {
                $this->addToLine($treeNode, $this->node->type);
            } else {
                // otherwise, assume it is a node, and resolve it
                $treeNode = $this->resolveNode($this->node->type, $treeNode);
            }
            $this->addToLine($treeNode, ' ');
        }
        // if the parameter is by reference, so note it
        if ($this->node->byRef) {
            $this->addToLine($treeNode, '&');
        }
        // add in the variable reference
        $this->addVariableReference($treeNode);
        return $treeNode;
    }
}
