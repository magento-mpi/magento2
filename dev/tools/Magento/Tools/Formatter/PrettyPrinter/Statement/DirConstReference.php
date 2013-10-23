<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgedeon
 * Date: 10/23/13
 * Time: 3:02 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Magento\Tools\Formatter\PrettyPrinter\Statement;


use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Scalar_DirConst;

class DirConstReference extends ReferenceAbstract
{
    /**
     * This method constructs a new statement based on the specified string
     * @param PHPParser_Node_Scalar_DirConst $node
     */
    public function __construct(PHPParser_Node_Scalar_DirConst $node)
    {
        parent::__construct($node);
    }
    /**
     * This method resolves the current statement, presumably held in the passed in tree node, into lines.
     *
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        /** @var Line $line */
        $line = $treeNode->getData();
        /*
        public function pScalar_DirConst(PHPParser_Node_Scalar_DirConst $node) {
            return '__DIR__';
        }
        */
        $line->add('__DIR__');
    }
}
