<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\HardLineBreak;
use Magento\Tools\Formatter\PrettyPrinter\IndentConsumer;
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
     * This method constructs a new statement based on the specified scalar.
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

    /**
     * This method reproduces the heredoc structure.
     * @param Line $line
     * @param $heredocCloseTag
     * @param array $bodyLines
     */
    protected function processHeredoc(Line $line, $heredocCloseTag, array $bodyLines, TreeNode $treeNode)
    {
        $line->add('<<<')->add($heredocCloseTag)->add(new HardLineBreak());
        foreach ($bodyLines as $bodyLine) {
            if (is_string($bodyLine)) {
                $heredocLines = explode(HardLineBreak::EOL, $bodyLine);
                if (!empty($heredocLines)) {
                    $heredocLineKeys = array_keys($heredocLines);
                    $lastKey = end($heredocLineKeys);
                    foreach ($heredocLines as $key => $heredocLine) {
                        $line->add(new IndentConsumer())->add($heredocLine);
                        // add in a newline if we are in the middle of the list or if the original has a newline
                        if ($lastKey !== $key || $this->endsWith($bodyLine, HardLineBreak::EOL)) {
                            $line->add(new HardLineBreak());
                        }
                    }
                }
            } else {
                $line->add('{');
                $this->resolveNode($bodyLine, $treeNode);
                $line->add('}');
            }
        }
        $line->add(new HardLineBreak())->add(new IndentConsumer())->add($heredocCloseTag);
    }
}
