<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Formatter\Tree;

use Magento\Tools\Formatter\Tree\Tree;
use Magento\Tools\Formatter\Tree\TreeNode;

/**
 * This class is used to test the tree functions.
 * Class TreeTest
 * @package Magento\Test\Tools\Formatter\Tree
 */
class TreeTest extends \PHPUnit_Framework_TestCase {
    /**
     * This method tests the basic workings of the tree functionality.
     */
    public function testNewTree() {
        $tree = new Tree();
        // add some nodes to our tree in a grandparent, parent, child relationship
        $tree->addChild($this->getNode('A'));
        $tree->addChild($this->getNode('B'));
        $tree->addChild($this->getNode('C'));
        // dump the tree
        $visitor = new DumpNodeVisitor();
        $tree->traverse($visitor);
        $this->assertEquals("A" . PHP_EOL . ".B" . PHP_EOL . "..C" . PHP_EOL, $visitor->result, "Tree dump does not look right.");
        // next test
        $tree->clear();
        // add some nodes to our tree in a parent, child, child relationship
        $tree->addChild($this->getNode('X'));
        $tree->addChild($this->getNode('Y1'), false);
        $tree->addChild($this->getNode('Y2'), false);
        // dump the tree
        $visitor = new DumpNodeVisitor();
        $tree->traverse($visitor);
        $this->assertEquals("X" . PHP_EOL . ".Y1" . PHP_EOL . ".Y2" . PHP_EOL, $visitor->result, "Tree dump does not look right.");
    }

    /**
     * This method returns a new node with the passed in data.
     * @param mixed $data User data for the node
     */
    protected function getNode($data) {
        $treeNode = new TreeNode($data);
        $this->assertEquals($data, $treeNode->getData(), "Original data not saved in the node.");

        return $treeNode;
    }
}