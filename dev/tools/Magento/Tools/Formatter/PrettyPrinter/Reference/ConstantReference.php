<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Expr_ConstFetch;

class ConstantReference extends AbstractReference
{
    /**
     * This member holds the tokens that need to be replaced.
     *
     * @var string[]
     */
    protected $replacements = ['false', 'true', 'null'];

    /**
     * This method constructs a new reference based on the specified constant.
     *
     * @param PHPParser_Node_Expr_ConstFetch $node
     */
    public function __construct(PHPParser_Node_Expr_ConstFetch $node)
    {
        parent::__construct($node);
    }

    /**
     * This method resolves the current reference, presumably held in the passed in tree node, into lines.
     *
     * @param TreeNode $treeNode Node containing the current statement.
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // get the node by name
        $this->resolveNode($this->node->name, $treeNode);
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // determine if it was one of the constants we are looking for and replace
        foreach ($this->replacements as $replacement) {
            if ($this->checkAndReplace($line, $replacement)) {
                break;
            }
        }
        return $treeNode;
    }

    /**
     * This method checks to see if the line ends with the specified replacement and replaces it if so.
     *
     * @param Line $line Line to look at.
     * @param string $replacement String containing the searched for value; assumes what is suppose to be replaced.
     * @return bool
     */
    protected function checkAndReplace(Line $line, $replacement)
    {
        $found = false;
        // get the last token
        $lastToken = $line->getLastToken();
        // if case insensitive search reveals it, then replace it
        if ($this->endsWith($lastToken, $replacement, true)) {
            $line->replaceEndOfToken($replacement);
            $found = true;
        }
        return $found;
    }
}
