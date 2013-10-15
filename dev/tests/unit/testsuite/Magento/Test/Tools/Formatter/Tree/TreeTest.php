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
class TreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This method tests the basic workings of the tree functionality.
     */
    public function testMultipleRoots()
    {
        $tree = new Tree();
        // add some nodes to our tree in a grandparent, parent, child relationship
        $tree->addRoot($this->getNode('A'));
        $tree->addChild($this->getNode('B'));
        $tree->addSibling($this->getNode('C'));
        $tree->addRoot($this->getNode('L1'));
        $tree->addChild($this->getNode('L2.1'));
        $tree->addSibling($this->getNode('L2.2'));
        // check results
        $this->compareTree(
            $tree,
            "A" . PHP_EOL . ".B" . PHP_EOL . ".C" . PHP_EOL . "L1" . PHP_EOL . ".L2.1" . PHP_EOL . ".L2.2" . PHP_EOL
        );
    }

    /**
     * This method tests the basic workings of the tree functionality.
     */
    public function testNewTree()
    {
        $tree = new Tree();
        // add some nodes to our tree in a grandparent, parent, child relationship
        $tree->addChild($this->getNode('A'));
        $tree->addChild($this->getNode('B'));
        $tree->addChild($this->getNode('C'));
        // check results
        $this->compareTree($tree, "A" . PHP_EOL . ".B" . PHP_EOL . "..C" . PHP_EOL);
        // next test
        $tree->clear();
        // add some nodes to our tree in a parent, child, child relationship
        $tree->addRoot($this->getNode('X'));
        $tree->addChild($this->getNode('Y1'), false);
        $tree->addChild($this->getNode('Y2'), false);
        // check results
        $this->compareTree($tree, "X" . PHP_EOL . ".Y1" . PHP_EOL . ".Y2" . PHP_EOL);
        // next test
        $tree->clear();
        // add some nodes to our tree in a parent, child, child relationship
        $tree->addRoot($this->getNode('X'));
        $tree->addChild($this->getNode('Y1'));
        $tree->addSibling($this->getNode('Y2'));
        $tree->addChild($this->getNode('Z1'));
        $tree->addSibling($this->getNode('Z2'));
        // check results
        $this->compareTree(
            $tree,
            "X" . PHP_EOL . ".Y1" . PHP_EOL . ".Y2" . PHP_EOL . "..Z1" . PHP_EOL . "..Z2" . PHP_EOL
        );
    }

    /**
     * This method tests the insertion of sibling nodes.
     */
    public function testSiblings()
    {
        $tree = new Tree();
        // add some nodes to our tree in a grandparent, parent, child relationship
        $nodeA = $tree->addChild($this->getNode('A'));
        $nodeB = $tree->addChild($this->getNode('B'));
        $nodeC = $tree->addSibling($this->getNode('C'));
        // check results
        $this->compareTree($tree, "A" . PHP_EOL . ".B" . PHP_EOL . ".C" . PHP_EOL);
        // add siblings
        $tree->setCurrentNode($nodeA);
        $tree->addSibling($this->getNode('A\''));
        $tree->setCurrentNode($nodeB);
        $tree->addSibling($this->getNode('B\''));
        $tree->setCurrentNode($nodeC);
        $tree->addSibling($this->getNode('C\''));
        // check results
        $this->compareTree(
            $tree,
            "A" . PHP_EOL . ".B" . PHP_EOL . ".B'" . PHP_EOL . ".C" . PHP_EOL . ".C'" . PHP_EOL . "A'" . PHP_EOL
        );
        // add root sibling to original node
        $tree->setCurrentNode($nodeA);
        $tree->addSibling($this->getNode("A''"));
        // check results
        $this->compareTree(
            $tree,
            "A" . PHP_EOL . ".B" . PHP_EOL . ".B'" . PHP_EOL . ".C" . PHP_EOL . ".C'" . PHP_EOL . "A''" . PHP_EOL .
            "A'" . PHP_EOL
        );
    }

    /**
     * This method dumps the tree and compares it to the passed in value.
     */
    protected function compareTree($tree, $expectedValue)
    {
        $visitor = new DumpNodeVisitor();
        $tree->traverse($visitor);
        $this->assertEquals($expectedValue, $visitor->result, "Tree dump does not look right.");
    }

    /**
     * This method returns a new node with the passed in data.
     * @param mixed $data User data for the node
     */
    protected function getNode($data)
    {
        $treeNode = new TreeNode($data);
        $this->assertEquals($data, $treeNode->getData(), "Original data not saved in the node.");

        return $treeNode;
    }
}
