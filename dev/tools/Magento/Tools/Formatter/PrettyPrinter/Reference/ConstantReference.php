<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_ConstFetch;

class ConstantReference extends AbstractReference
{
    /**
     * This method constructs a new reference based on the specified constant.
     * @param PHPParser_Node_Expr_ConstFetch $node
     */
    public function __construct(PHPParser_Node_Expr_ConstFetch $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current reference, presumably held in the passed in tree node, into lines.
     * @param TreeNode $treeNode Node containing the current statement.
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // get the node by name
        $this->resolveNode($this->node->name, $treeNode);
        // retrieve the tokens array
        $tokens = $treeNode->getData()->line->getTokens();
        // get the last item in the array
        $result = $tokens[sizeof($tokens) - 1];
        if (
            strcasecmp('FALSE', $result) === 0 || strcasecmp('TRUE', $result) === 0 || strcasecmp('NULL', $result) === 0
        ) {
            $tokens[sizeof($tokens) - 1] = strtolower($result);
            // reset the last item in the array due to php's "copy-on-write" rule for arrays
            $treeNode->getData()->line->setTokens($tokens);
        }
    }
}
