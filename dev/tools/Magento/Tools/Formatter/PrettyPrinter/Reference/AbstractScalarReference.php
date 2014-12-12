<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\ParserLexer;
use Magento\Tools\Formatter\PrettyPrinter\HardConditionalLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\HeredocTerminatingLineCondition;
use Magento\Tools\Formatter\PrettyPrinter\IndentConsumer;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Scalar;

/**
 * This class will return the string passed in.
 * Class ScalarReference
 */
class AbstractScalarReference extends AbstractReference
{
    /**
     * @var mixed
     */
    protected $result;

    /**
     * This method constructs a new statement based on the specified scalar.
     *
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
     * @return TreeNode
     */
    public function resolve(TreeNode $treeNode)
    {
        parent::resolve($treeNode);
        // optionally add in the result
        if (null !== $this->result) {
            // add in the constant value
            $this->addToLine($treeNode, $this->result);
        }
        return $treeNode;
    }

    /**
     * This method reproduces the heredoc structure.
     *
     * @param TreeNode $treeNode
     * @param string $heredocCloseTag String containing the value of the heredoc tag
     * @param string|array $bodyLines string or Array containing the body lines of the heredoc.
     * @return void
     */
    protected function processHeredoc(TreeNode $treeNode, $heredocCloseTag, $bodyLines)
    {
        // if this is a now doc add the single quote to the open tag
        $isNowDoc = $this->node->getAttribute(ParserLexer::IS_NOWDOC, false);
        $this->addToLine($treeNode, '<<<')->add($isNowDoc ? "'" . $heredocCloseTag . "'" : $heredocCloseTag);
        $this->addToLine($treeNode, new HardLineBreak());
        // if the lines of the body specified by an array, then print them out individually since
        // it may be lines and node references
        if (is_array($bodyLines)) {
            foreach ($bodyLines as $bodyLine) {
                if (is_string($bodyLine)) {
                    $heredocLines = explode(HardLineBreak::EOL, $bodyLine);
                    if (!empty($heredocLines)) {
                        $heredocLineKeys = array_keys($heredocLines);
                        $lastKey = end($heredocLineKeys);
                        foreach ($heredocLines as $key => $heredocLine) {
                            $this->addToLine($treeNode, new IndentConsumer())->add($heredocLine);
                            // add in a newline if we are in the middle of the list or if the original has a newline
                            if ($lastKey !== $key || $this->endsWith($bodyLine, HardLineBreak::EOL)) {
                                $this->addToLine($treeNode, new HardLineBreak());
                            }
                        }
                    }
                } else {
                    $this->addToLine($treeNode, '{');
                    $treeNode = $this->resolveNode($bodyLine, $treeNode);
                    $this->addToLine($treeNode, '}');
                }
            }
        } else {
            // otherwise, just add the line
            $this->addToLine($treeNode, new IndentConsumer())->add($bodyLines);
        }
        $this->addToLine($treeNode, new IndentConsumer())->add($heredocCloseTag);
        $this->addToLine($treeNode, new HardConditionalLineBreak(new HeredocTerminatingLineCondition()));
    }
}
