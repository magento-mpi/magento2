<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Name;

class ClassReference extends AbstractReference
{
    /**
     * This method constructs a new statement based on the specified name.
     * @param PHPParser_Node_Name $node
     */
    public function __construct(PHPParser_Node_Name $node)
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
        // Add the preceding \ if this is a fully qualified name
        if ($this->node->isFullyQualified()) {
            $this->addToLine($treeNode, '\\');
        }
        // add the name to the end of the current line
        $this->addToLine($treeNode, (string)$this->node);
        return $treeNode;
    }
}
