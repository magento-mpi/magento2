<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */


namespace Magento\Tools\Formatter\Tree;


class FindParent extends NodeVisitorAbstract
{
    /**
     * This member holds the parent node
     * @var TreeNode
     */
    public $parent;

    /**
     * This member holds the node which is being searched for.
     * @var TreeNode
     */
    protected $search;

    /**
     * This method constructs a new instance to search for the parent of the passed in node.
     * @param TreeNode $search Node for which the parent is being searched for.
     */
    public function __construct(TreeNode $search)
    {
        $this->search = $search;
    }

    /**
     * This method is called when first visiting a node.
     * @param TreeNode $treeNode
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        if (null === $this->parent && $treeNode->hasChildren()) {
            foreach ($treeNode->getChildren() as $child) {
                if ($this->search === $child) {
                    // found the parent, so tag it and break out of the loop
                    $this->parent = $treeNode;
                    break;
                }
            }
        }
    }
}
