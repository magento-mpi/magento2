<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;

/**
 * This class is the base class for all printer statements.
 */
abstract class StatementAbstract implements Node
{
    const ATTRIBUTE_COMMENTS = 'comments';

    /**
     * This member holds the current node.
     * @var \PHPParser_NodeAbstract
     */
    protected $node;

    /**
     * This method constructs a new statement based on the specify node.
     * @param \PHPParser_NodeAbstract $node
     */
    public function __construct(\PHPParser_NodeAbstract $node)
    {
        $this->node = $node;
    }

    /**
     * This method adds any comments in the current node to the passed in tree.
     * @param Tree $tree
     */
    protected function addComments(Tree $tree)
    {
        /* Reference
           $comments = $this->pComments($node->getAttribute('comments', array()));
        */
        // only attempt to add comments if they are present
        if ($this->node->hasAttribute(self::ATTRIBUTE_COMMENTS)) {
            // add individual lines of the comments to the tree
            $comments = $this->node->getAttribute(self::ATTRIBUTE_COMMENTS);
            foreach ($comments as $comment) {
                // split the lines so that they can be indented correctly
                $commentLines = explode(HardLineBreak::EOL, $comment->getReformattedText());
                foreach ($commentLines as $commentLine) {
                    // add the line individually to the tree so that they can be indented correctly
                    $tree->addSibling(new TreeNode((new Line($commentLine))->add(new HardLineBreak())));
                }
            }
        }
    }

    /**
     * This method adds modifiers to the the line based on the bit map passed in.
     * @param mixed $modifiers Bit map containing the markers for the various modifiers.
     * @param Line $line Instance of line to add modifier.
     */
    protected function addModifier($modifiers, Line $line)
    {
        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) {
            $line->add('abstract ');
        }

        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_FINAL) {
            $line->add('final ');
        }

        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC) {
            $line->add('public ');
        }

        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED) {
            $line->add('protected ');
        }

        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE) {
            $line->add('private ');
        }

        if ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_STATIC) {
            $line->add('static ');
        }
    }

    /**
     * This method adds the arguments to the current line
     * @param array $arguments
     */
    protected function processArgumentList(array $arguments, Tree $tree, Line $line)
    {
        foreach ($arguments as $index => $argument) {
            $line->add(new ConditionalLineBreak(' '));

            Printer::processStatement($argument, $tree);

            if ($index < sizeof($arguments) - 1) {
                $line->add(',');
            }
        }
    }

    /**
     * This method returns the full name of the class.
     *
     * @return string Full name of the class is called through.
     */
    public static function getType()
    {
        return get_called_class();
    }
}
