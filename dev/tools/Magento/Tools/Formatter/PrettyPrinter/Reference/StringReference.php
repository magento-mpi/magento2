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
use Magento\Tools\Formatter\PrettyPrinter\IndentConsumer;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;
use PHPParser_Node_Scalar_String;

class StringReference extends AbstractScalarReference
{
    /**
     * This method constructs a new statement based on the specified string
     * @param PHPParser_Node_Scalar_String $node
     */
    public function __construct(PHPParser_Node_Scalar_String $node)
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
        /* Reference
        $result = $node->getAttribute(ParserLexer::ORIGINAL_VALUE);
        $heredocCloseTag = $node->getAttribute(ParserLexer::HEREDOC_CLOSE_TAG);
        if (null !== $heredocCloseTag) {
            $result = $this->processHeredoc($heredocCloseTag, $this->preventIndent($node->value));
        } elseif (null === $result) {
            // if nothing there, then use the base class version
            $result = parent::pScalar_String($node);
        }
        return $result;

        return '\'' . $this->pNoIndent(addcslashes($node->value, '\'\\')) . '\'';

        $result = '<<<' . $heredocCloseTag . self::EOL . $this->noIndentToken;
        $result .= $body;
        $result .= self::EOL . $this->noIndentToken . $heredocCloseTag;
        return $result;
        */
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        // if the original value exists, just use that so that the number representation does not change
        $stringValue = $this->node->getAttribute(ParserLexer::ORIGINAL_VALUE);
        $heredocCloseTag = $this->node->getAttribute(ParserLexer::HEREDOC_CLOSE_TAG);
        if (null !== $heredocCloseTag) {
            $this->processHeredoc($line, $heredocCloseTag, $this->node->value);
        } elseif (null === $stringValue) {
            // if nothing there, then use the raw data
            $line->add('\'')->add(addcslashes($this->node->value, '\'\\'))->add('\'');
        } else {
            $line->add($stringValue);
        }
    }

    /**
     * This method reproduces the heredoc structure.
     * @param Line $line
     * @param $heredocCloseTag
     * @param $body
     */
    protected function processHeredoc(Line $line, $heredocCloseTag, $body)
    {
        $line->add('<<<')->add($heredocCloseTag)->add(new HardLineBreak());
        $heredocLines = explode(HardLineBreak::EOL, $body);
        if (!empty($heredocLines)) {
            foreach ($heredocLines as $heredocLine) {
                $line->add(new IndentConsumer())->add($heredocLine)->add(new HardLineBreak());
            }
        }
        $line->add(new IndentConsumer())->add($heredocCloseTag);
    }
}