<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\Tree;


/**
 * This class is used to test the tree functions.
 * Class TreeTest
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
        $nodeA = $tree->addRoot($this->getNode('A'));
        $nodeB = $nodeA->addChild($this->getNode('B'));
        $nodeB->addSibling($this->getNode('C'));
        $nodeL1 = $tree->addRoot($this->getNode('L1'));
        $nodeL2 = $nodeL1->addChild($this->getNode('L2.1'));
        $nodeL2->addSibling($this->getNode('L2.2'));
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
        $nodeA = $tree->addChild($this->getNode('A'));
        $nodeB = $nodeA->addChild($this->getNode('B'));
        $nodeB->addChild($this->getNode('C'));
        // check results
        $this->compareTree($tree, "A" . PHP_EOL . ".B" . PHP_EOL . "..C" . PHP_EOL);
        // next test
        $tree->clear();
        // add some nodes to our tree in a parent, child, child relationship
        $nodeX = $tree->addRoot($this->getNode('X'));
        $nodeX->addChild($this->getNode('Y1'));
        $nodeX->addChild($this->getNode('Y2'));
        // check results
        $this->compareTree($tree, "X" . PHP_EOL . ".Y1" . PHP_EOL . ".Y2" . PHP_EOL);
        // next test
        $tree->clear();
        // add some nodes to our tree in a parent, child, child relationship
        $nodeX = $tree->addRoot($this->getNode('X'));
        $nodeY1 = $nodeX->addChild($this->getNode('Y1'));
        $nodeY2 = $nodeY1->addSibling($this->getNode('Y2'));
        $nodeZ1 = $nodeY2->addChild($this->getNode('Z1'));
        $nodeZ1->addSibling($this->getNode('Z2'));
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
        $nodeB = $nodeA->addChild($this->getNode('B'));
        $nodeC = $nodeB->addSibling($this->getNode('C'));
        // check results
        $this->compareTree($tree, "A" . PHP_EOL . ".B" . PHP_EOL . ".C" . PHP_EOL);
        // add siblings
        $nodeAP = $nodeA->addSibling($this->getNode('A\''));
        $nodeB->addSibling($this->getNode('B\''));
        $nodeC->addSibling($this->getNode('C\''));
        // check results
        $this->compareTree(
            $tree,
            "A" . PHP_EOL . ".B" . PHP_EOL . ".B'" . PHP_EOL . ".C" . PHP_EOL . ".C'" . PHP_EOL . "A'" . PHP_EOL
        );
        // add root sibling to original node
        $nodeA->addSibling($this->getNode("A''"));
        $nodeAP->addSibling($this->getNode("A'''"), false);
        // check results
        $this->compareTree(
            $tree,
            "A" .
            PHP_EOL .
            ".B" .
            PHP_EOL .
            ".B'" .
            PHP_EOL .
            ".C" .
            PHP_EOL .
            ".C'" .
            PHP_EOL .
            "A''" .
            PHP_EOL .
            "A'''" .
            PHP_EOL .
            "A'" .
            PHP_EOL
        );
    }

    /**
     * This method test manipulating a tree just using the nodes.
     */
    public function testNodeManipulation()
    {
        $tree = new Tree();
        // add some nodes to our tree
        $nodeA = $tree->addRoot($this->getNode('A'));
        $nodeB2 = $nodeA->addChild($this->getNode("B2"));
        $this->assertEquals($nodeA, $nodeB2->getParent(), "Child node's parent is not set!");
        $nodeB1 = $nodeB2->addSibling($this->getNode("B1"), false);
        $this->assertEquals($nodeA, $nodeB1->getParent(), "Child node's parent is not set!");
        $nodeB3 = $nodeB2->addSibling($this->getNode("B3"), true);
        $this->assertEquals($nodeA, $nodeB3->getParent(), "Child node's parent is not set!");
        // check results
        $this->compareTree($tree, "A" . PHP_EOL . ".B1" . PHP_EOL . ".B2" . PHP_EOL . ".B3" . PHP_EOL);
    }

    /**
     * This method dumps the tree and compares it to the passed in value.
     * @param Tree $tree
     * @param $expectedValue
     */
    protected function compareTree(Tree $tree, $expectedValue)
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
