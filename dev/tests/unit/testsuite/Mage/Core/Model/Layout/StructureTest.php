<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Core
 */
/**
 * Test class for Mage_Core_Model_Layout_Structure.
 */
class Mage_Core_Model_Layout_StructureTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_Structure
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Layout_Structure;
    }

    public function testGetParentName()
    {
        $parent = 'parent';
        $child = 'child';
        $this->_model->insertElement('', $parent, 'container');
        $this->assertEmpty($this->_model->getParentName($parent));

        $this->_model->insertElement($parent, $child, 'block');
        $parentName = $this->_model->getParentName($child);
        $this->assertInternalType('string', $parentName);
        $this->assertEquals($parent, $parentName);
    }

    public function testGetChildNames()
    {
        $parent = 'parent';
        $children = array('child1', 'child2', 'child3');

        $this->_model->insertContainer('', $parent);
        foreach ($children as $child) {
            $this->_model->insertElement($parent, $child, 'block');
        }
        $childNames = $this->_model->getChildNames($parent);
        $this->assertEquals($children, $childNames);
    }

    public function testSetChild()
    {
        $parent = 'parent';
        $child = 'child';
        $alias = 'alias';
        $this->_model->insertContainer('', $parent);
        $this->assertEmpty($this->_model->getChildNames($parent));
        $this->_model->setChild($parent, $child, $alias);
        $this->assertEquals($child, $this->_model->getChildName($parent, $alias));
    }

    public function testGetElementAlias()
    {
        $alias = 'alias';
        $name = 'name';
        $this->_model->insertBlock('', $name, $alias);
        $this->assertEquals($alias, $this->_model->getElementAlias($name));
    }

    /**
     * @covers Mage_Core_Model_Layout_Structure::setElementAttribute
     * @covers Mage_Core_Model_Layout_Structure::getElementAttribute
     */
    public function testSetGetElementAttribute()
    {
        $name1 = 'name1';
        $name2 = 'name2';
        $alias = 'alias';
        $attr = 'attr';

        $this->assertEmpty($this->_model->getElementAttribute($name1, 'name'));

        $this->_model->insertBlock('', $name1);

        $this->assertTrue($this->_model->setElementAttribute($name1, 'name', $name2));
        $this->assertEmpty($this->_model->getElementAttribute($name1, 'name'));
        $this->assertEquals($name2, $this->_model->getElementAttribute($name2, 'name'));

        $this->assertFalse($this->_model->setElementAttribute($name1, 'alias', $alias));

        $this->assertTrue($this->_model->setElementAttribute($name2, 'alias', $alias));
        $this->assertEquals($alias, $this->_model->getElementAttribute($name2, 'alias'));

        $this->assertTrue($this->_model->setElementAttribute($name2, 'attr', $attr));
        $this->assertEquals($attr, $this->_model->getElementAttribute($name2, 'attr'));

        $this->assertTrue($this->_model->setElementAttribute($name2, 'attr', $attr));
    }

    public function testMove()
    {
        $parent1 = 'parent1';
        $parent2 = 'parent2';
        $block1 = 'block1';
        $block2 = 'block2';

        $this->_model->insertContainer('', $parent1);
        $this->_model->insertContainer('', $parent2);
        $this->_model->insertBlock('', $block1);
        $this->_model->insertBlock('', $block2);
        $this->assertEmpty($this->_model->getChildNames($parent1));
        $this->assertEmpty($this->_model->getChildNames($parent2));
        $this->_model->move($block1, $parent1);
        $this->_model->move($block2, $parent2);
        $this->assertEquals(array($block1), $this->_model->getChildNames($parent1));
        $this->assertEquals(array($block2), $this->_model->getChildNames($parent2));
        $this->_model->move($block2, $parent1);
        $this->assertEquals(array($block1, $block2), $this->_model->getChildNames($parent1));
        $this->assertEmpty($this->_model->getChildNames($parent2));
    }

    public function testUnsetElement()
    {
        $name = 'name';
        $this->_model->insertBlock('', $name);
        $this->assertTrue($this->_model->hasElement($name));
        $this->_model->unsetElement($name);
        $this->assertFalse($this->_model->hasElement($name));
    }

    public function testGetChildName()
    {
        $parent = 'parent';
        $child = 'child';
        $alias = 'alias';
        $this->_model->insertBlock('', $parent);
        $this->assertFalse($this->_model->getChildName($parent, $alias));
        $this->_model->insertBlock($parent, $child, $alias);
        $result = $this->_model->getChildName($parent, $alias);
        $this->assertEquals($child, $result);
        $this->assertInternalType('string', $result);
    }

    public function testGetChildNameWithBrokenRef()
    {
        $parent = 'parent';
        $child = 'child';
        $alias = 'alias';
        $this->_model->insertBlock($parent, $child, $alias);
        $this->assertEquals($parent, $this->_model->getElementAttribute($child, 'broken_parent_name'));
        $this->assertFalse($this->_model->getChildName($parent, $alias));
        $this->_model->insertBlock('', $parent);
        $result = $this->_model->getChildName($parent, $alias);
        $this->assertEquals($child, $result);
        $this->assertInternalType('string', $result);
        $this->assertEmpty($this->_model->getElementAttribute($child, 'broken_parent_name'));
    }

    /**
     * @dataProvider elementsDataProvider
     */
    public function testInsertElement($parentName, $name, $type, $alias = '', $after = true, $sibling = '',
        $options = array(), $expected
    ) {
        $this->_model->insertElement($parentName, $name, $type, $alias, $after, $sibling, $options);
        $this->assertEquals($expected, $this->_model->hasElement($name));
    }

    public function elementsDataProvider()
    {
        return array(
            array('root', 'name', 'block', 'alias', true, 'sibling', array('htmlTag' => 'div'), true),
            array('root', 'name', 'container', 'alias', true, 'sibling', array('htmlTag' => 'div'), true),
            array('', 'name', 'block', 'alias', true, 'sibling', array('htmlTag' => 'div'), true),
            array('root', 'name', 'invalid_type', 'alias', true, 'sibling', array('htmlTag' => 'div'), false),
            array('root', 'name', 'block', 'alias', false, 'sibling', array('htmlTag' => 'div'), true),
            array('root', 'name', 'block', 'alias', true, 'sibling', array(), true),
        );
    }

    public function testInsertElementWithoutName()
    {
        $name = $this->_model->insertElement('root', '', 'block');
        $this->assertTrue($this->_model->hasElement($name));
        $this->assertEquals(Mage_Core_Model_Layout_Structure::TMP_NAME_PREFIX . '0', $name);

        $this->_model->insertElement('root', 'name', 'block');
        $name = $this->_model->insertElement('root', '', 'block');
        $this->assertTrue($this->_model->hasElement($name));
        $this->assertEquals(Mage_Core_Model_Layout_Structure::TMP_NAME_PREFIX . '1', $name);
    }

    public function testInsertElementWithoutAlias()
    {
        $root = 'root';
        $name = 'name';

        $this->_model->insertContainer('', $root);
        $this->_model->insertElement($root, $name, 'block');
        $alias = $this->_model->getElementAlias($name);
        $this->assertEquals($name, $alias);

        $foundName = $this->_model->getChildName($root, $alias);
        $this->assertEquals($name, $foundName);
    }

    public function testInsertElementOrder()
    {
        $root = 'root';
        $name1 = 'name1';
        $name2 = 'name2';
        $name3 = 'name3';
        $name4 = 'name4';
        $name5 = 'name5';

        $this->_model->insertElement('', $root, 'container');

        $this->_model->insertElement($root, $name1, 'block');
        $this->_model->insertElement($root, $name2, 'block', '', true, $name1);
        $children = $this->_model->getChildNames($root);
        $this->assertEquals(array($name1, $name2), $children);

        $this->_model->insertElement($root, $name3, 'block', '', false, $name1);
        $children = $this->_model->getChildNames($root);
        $this->assertEquals(array($name3, $name1, $name2), $children);

        $this->_model->insertElement($root, $name4, 'block');
        $children = $this->_model->getChildNames($root);
        $this->assertEquals(array($name3, $name1, $name2, $name4), $children);

        $this->_model->insertElement($root, $name5, 'block', '', false);
        $children = $this->_model->getChildNames($root);
        $this->assertEquals(array($name5, $name3, $name1, $name2, $name4), $children);
    }

    public function testInsertBlock()
    {
        $name = 'name';
        $this->_model->insertBlock('', $name);
        $this->assertTrue($this->_model->hasElement($name));
        $this->assertTrue($this->_model->isBlock($name));
    }

    public function testInsertContainer()
    {
        $name = 'name';
        $this->_model->insertContainer('', $name);
        $this->assertTrue($this->_model->hasElement($name));
        $this->assertFalse($this->_model->isBlock($name));
    }

    /**
     * @covers Mage_Core_Model_Layout_Structure::hasElement
     * @covers Mage_Core_Model_Layout_Structure::unsetChild
     */
    public function testHasElement()
    {
        $parent = 'parent';
        $child = 'name';
        $this->_model->insertBlock('', $parent);
        $this->assertFalse($this->_model->hasElement($child));
        $this->_model->insertBlock($parent, $child);
        $this->assertTrue($this->_model->hasElement($child));
        $this->_model->unsetChild($parent, $child);
        $this->assertFalse($this->_model->hasElement($child));
    }

    public function testGetChildrenCount()
    {
        $root = 'root';
        $child = 'block';
        $this->_model->insertBlock('', $root);
        $this->assertEquals(0, $this->_model->getChildrenCount($root));
        $this->_model->insertBlock($root, $child);
        $this->assertEquals(1, $this->_model->getChildrenCount($root));
        $this->_model->unsetChild($root, $child);
        $this->assertEquals(0, $this->_model->getChildrenCount($root));
    }

    /**
     * @covers Mage_Core_Model_Layout_Structure::addToParentGroup
     * @covers Mage_Core_Model_Layout_Structure::getGroupChildNames
     */
    public function testAddGetGroup()
    {
        $parent = 'parent';
        $child1 = 'child1';
        $child2 = 'child2';
        $group1 = 'group1';
        $group2 = 'group2';
        $this->_model->insertContainer('', $parent);
        $this->_model->insertBlock($parent, $child1);
        $this->assertEmpty($this->_model->getGroupChildNames($parent, $group1));
        $this->assertEmpty($this->_model->getGroupChildNames($parent, $group2));
        $this->_model->addToParentGroup($child1, $parent, $group1);
        $this->_model->insertBlock($parent, $child2);
        $this->_model->addToParentGroup($child2, $parent, $group2);
        $this->assertEquals(array($child1), $this->_model->getGroupChildNames($parent, $group1));
        $this->assertEquals(array($child2), $this->_model->getGroupChildNames($parent, $group2));
    }

    /**
     * @covers Mage_Core_Model_Layout_Structure::isBlock
     * @covers Mage_Core_Model_Layout_Structure::isContainer
     */
    public function testIsBlockIsContainer()
    {
        $block = 'block';
        $container = 'container';
        $invalidType = 'invalid';

        $this->_model->insertBlock('', $block);
        $this->_model->insertContainer('', $container);
        $this->_model->insertElement('', $invalidType, $invalidType);

        $this->assertTrue($this->_model->isBlock($block));
        $this->assertFalse($this->_model->isBlock($container));
        $this->assertFalse($this->_model->isBlock($invalidType));

        $this->assertFalse($this->_model->isContainer($block));
        $this->assertTrue($this->_model->isContainer($container));
        $this->assertFalse($this->_model->isContainer($invalidType));
    }

    /**
     * @covers Mage_Core_Model_Layout_Structure::markOutput
     * @covers Mage_Core_Model_Layout_Structure::getOutputList
     */
    public function testMarkGetOutput()
    {
        $blockName = 'name';
        $containerName = 'container';
        $childBlock = 'child';
        $childContainer = 'child_container';
        $this->assertEmpty($this->_model->getElementAttribute($blockName, 'output'));
        $this->assertEmpty($this->_model->getElementAttribute($containerName, 'output'));
        $this->assertEmpty($this->_model->getElementAttribute($childBlock, 'output'));
        $this->assertEmpty($this->_model->getElementAttribute($childContainer, 'output'));

        $this->assertEquals(array(), $this->_model->getOutputList());

        $this->_model->insertContainer('', $containerName);
        $this->_model->insertBlock('', $blockName);
        $this->_model->insertBlock($containerName, $childBlock);
        $this->_model->insertBlock($containerName, $childContainer);
        $this->assertEmpty($this->_model->getElementAttribute($blockName, 'output'));
        $this->assertEmpty($this->_model->getElementAttribute($containerName, 'output'));
        $this->assertEmpty($this->_model->getElementAttribute($childBlock, 'output'));
        $this->assertEmpty($this->_model->getElementAttribute($childContainer, 'output'));

        // root containers are always in output list, they should not be marked additionally
        $this->assertEquals(array($containerName), $this->_model->getOutputList());

        $this->_model->markOutput($containerName);
        // root containers should be in output list
        $this->assertEquals(array($containerName), $this->_model->getOutputList());
        $this->_model->markOutput($blockName);
        // root blocks should be in output list
        $this->assertEquals(array($containerName, $blockName), $this->_model->getOutputList());
        $this->_model->markOutput($childBlock);
        // child blocks should not be in output list
        $this->assertEquals(array($containerName, $blockName), $this->_model->getOutputList());
        $this->_model->markOutput($childContainer);
        // child containers should not be in output list
        $this->assertEquals(array($containerName, $blockName), $this->_model->getOutputList());

        // root block should be marked
        $this->assertEquals('1', $this->_model->getElementAttribute($blockName, 'output'));
        // container should not be marked
        $this->assertEmpty($this->_model->getElementAttribute($containerName, 'output'));
        // not root blocks should not be marked
        $this->assertEmpty($this->_model->getElementAttribute($childBlock, 'output'));
        // not root containers should not be marked
        $this->assertEmpty($this->_model->getElementAttribute($childContainer, 'output'));
    }
}
