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

    /**
     * @covers Mage_Core_Model_Layout_Structure::setChild
     * @todo Implement testSetChild().
     */
    public function testSetChild()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
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

    /**
     * @covers Mage_Core_Model_Layout_Structure::move
     * @todo Implement testMove().
     */
    public function testMove()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Mage_Core_Model_Layout_Structure::unsetChild
     * @todo Implement testUnsetChild().
     */
    public function testUnsetChild()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Mage_Core_Model_Layout_Structure::unsetElement
     * @todo Implement testUnsetElement().
     */
    public function testUnsetElement()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Mage_Core_Model_Layout_Structure::getChildName
     * @todo Implement testGetChildName().
     */
    public function testGetChildName()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
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
        $this->assertEquals('STRUCTURE_TMP_NAME_0', $name);

        $this->_model->insertElement('root', 'name', 'block');
        $name = $this->_model->insertElement('root', '', 'block');
        $this->assertTrue($this->_model->hasElement($name));
        $this->assertEquals('STRUCTURE_TMP_NAME_1', $name);
    }

    public function testInsertElementWithoutAlias()
    {
        $root = 'root';
        $name = 'name';

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

    public function testHasElement()
    {
        $name = 'name';
        $this->assertFalse($this->_model->hasElement($name));
        $this->_model->insertBlock('', $name);
        $this->assertTrue($this->_model->hasElement($name));
        // @todo: add remove
    }

    public function testGetChildrenCount()
    {
        $root = 'root';
        $this->_model->insertBlock('', $root);

        $this->assertEquals(0, $this->_model->getChildrenCount($root));
        $this->_model->insertBlock($root, '');
        $this->assertEquals(1, $this->_model->getChildrenCount($root));
        // @todo: add remove
    }

    /**
     * @covers Mage_Core_Model_Layout_Structure::addToParentGroup
     * @todo Implement testAddToParentGroup().
     */
    public function testAddToParentGroup()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Mage_Core_Model_Layout_Structure::getGroupChildNames
     * @todo Implement testGetGroupChildNames().
     */
    public function testGetGroupChildNames()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testIsBlock()
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
    }

    /**
     * @covers Mage_Core_Model_Layout_Structure::getStartNode
     * @todo Implement testGetStartNode().
     */
    public function testGetStartNode()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
