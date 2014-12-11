<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\TreeNode;

class NodePrinter extends LevelNodeVisitor
{
    /**
     * This member holds what is being used as a prefix to the line (i.e. 4 spaces).
     */
    const PREFIX = '    ';

    /**
     * This member holds the result of the traversal.
     *
     * @var string
     */
    public $result = '';

    /**
     * This method is called when first visiting a node.
     *
     * @param TreeNode $treeNode
     * @return void
     */
    public function nodeEntry(TreeNode $treeNode)
    {
        parent::nodeEntry($treeNode);
        // add the line data base on indents
        $line = $treeNode->getData()->line->getLine();
        // only prepend the prefix if the line is more than a LF
        if (strlen($line) > 1 && !$treeNode->getData()->line->isNoIndent()) {
            $line = str_repeat(self::PREFIX, $this->level) . $line;
        }
        // dump an error to the console if the line is long (+1 is to allow for \n at the end)
        if (NodeLeveler::MAX_LINE_LENGTH + 1 < strlen($line)) {
            echo "Warning: Line Longer Than Max (" . strlen($line) . " > " . NodeLeveler::MAX_LINE_LENGTH . ')';
            echo "\n-----\n{$line}\n-----\n";
        }
        // add the resulting string
        $this->result .= $line;
        // if the newly added line was a use statement, clean up lines between them
        if ($this->startsWith($line, "use ")) {
            $this->cleanupUseStatements();
        }
    }

    /**
     * @return void
     */
    protected function cleanupUseStatements()
    {
        // remove blank lines between use statements
        do {
            $count = 0;
            $this->result = preg_replace(
                '~\\nuse (.*);\\n\\nuse ~',
                HardLineBreak::EOL . 'use $1;' . HardLineBreak::EOL . 'use ',
                $this->result,
                1,
                $count
            );
        } while ($count > 0);
    }

    /**
     * This method returns true if $haystack start with the string in $needle.
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    protected function startsWith($haystack, $needle)
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}
