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

    public function testGetChildBeforeParent()
    {
        $parent = 'parent';
        $child = 'child';
        $alias = 'alias';
        $this->_model->insertBlock($parent, $child, $alias);
        $this->assertEmpty($this->_model->getElementAttribute($parent, 'type'));
        $this->assertEquals($parent, $this->_model->getParentName($child));
        $this->assertEquals($child, $this->_model->getChildName($parent, $alias));
        $this->_model->insertBlock('', $parent);
        $result = $this->_model->getChildName($parent, $alias);
        $this->assertEquals($child, $result);
    }

    public function testSetGetElementAlias()
    {
        $alias1 = 'alias1';
        $alias2 = 'alias1';
        $name = 'name';
        $this->_model->insertBlock('', $name, $alias1);
        $this->assertEquals($alias1, $this->_model->getElementAlias($name));
        $this->_model->setElementAlias($name, $alias2);
        $this->assertEquals($alias2, $this->_model->getElementAlias($name));
    }

    public function testRenameElement()
    {
        $name1 = 'name1';
        $name2 = 'name2';

        $this->assertFalse($this->_model->hasElement($name1));
        $this->_model->insertBlock('', $name1);
        $this->assertTrue($this->_model->hasElement($name1));
        $this->_model->renameElement($name1, $name2);
        $this->assertFalse($this->_model->hasElement($name1));
        $this->assertTrue($this->_model->hasElement($name2));
        $this->_model->renameElement($name2, '');
        $this->assertTrue($this->_model->hasElement($name2));
        $this->assertFalse($this->_model->hasElement(''));
    }

    public function testGetElementAttribute()
    {
        $name = 'name';
        $options = array('attribute' => 'value');
        $this->_model->insertElement('', $name, 'block', '', null, null, $options);
        $this->assertEquals($options['attribute'], $this->_model->getElementAttribute($name, 'attribute'));
        $this->assertEquals('', $this->_model->getElementAttribute($name, 'invalid_attribute'));
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

    public function testUnsetChild()
    {
        $parent = 'parent';
        $child = 'child';
        $this->_model->insertBlock($parent, $child);
        $this->_model->unsetChild($parent, $child);
        $this->_model->insertBlock('', $parent);
        $this->assertEmpty($this->_model->getParentName($child));
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
        $this->_model->addToParentGroup($child1, $group1);
        $this->_model->insertBlock($parent, $child2);
        $this->_model->addToParentGroup($child2, $group2);
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

    public function testIsManipulationAllowed()
    {
        // non-existing elements
        $this->assertFalse($this->_model->isManipulationAllowed('block2'));
        $this->assertFalse($this->_model->isManipulationAllowed('block3'));

        // block under block
        $this->assertEquals('block1', $this->_model->insertBlock('', 'block1'));
        $this->assertEquals('block2', $this->_model->insertBlock('block1', 'block2'));
        $this->assertFalse($this->_model->isManipulationAllowed('block2'));

        // block under container
        $this->assertEquals('container1', $this->_model->insertContainer('', 'container1'));
        $this->assertEquals('block3', $this->_model->insertBlock('container1', 'block3'));
        $this->assertTrue($this->_model->isManipulationAllowed('block3'));

        // container under container
        $this->assertEquals('container2', $this->_model->insertContainer('container1', 'container2'));
        $this->assertTrue($this->_model->isManipulationAllowed('container2'));
    }

    /**
     * @param array $instructions
     * @param array $expectations
     * @dataProvider sortElementsDataProvider
     */
    public function testSortElements($instructions, $expectations)
    {
        if (isset($expectations['incomplete'])) {
            $this->markTestIncomplete('MAGETWO-839');
        }
        $this->_buildLayout($instructions);

        $this->_model->sortElements();

        foreach ($expectations as $parentName => $expectedChildNames) {
            $actualChildNames = $this->_model->getChildNames($parentName);
            $this->assertEquals($expectedChildNames, $actualChildNames);
        }
    }

    /**
     * Goes through instructions and adds blocks and containers to build a required layout structure
     *
     * @param array $instructions
     */
    protected function _buildLayout($instructions)
    {
        $this->_model->insertBlock('', 'root');
        foreach ($instructions as $instruction) {
            $parentName = isset($instruction['parent']) ? $instruction['parent'] : 'root';
            $name = $instruction['name'];
            if (isset($instruction['before'])) {
                $after = false;
                $sibling = $instruction['before'];
            } else if (isset($instruction['after'])) {
                $after = true;
                $sibling = $instruction['after'];
            } else {
                $after = null;
                $sibling = null;
            }
            $type = isset($instruction['type'])
                ? $instruction['type'] : Mage_Core_Model_Layout_Structure::ELEMENT_TYPE_BLOCK;
            $this->_model->insertElement($parentName, $name, $type, '', $after, $sibling);
        }
    }

    /**
     * @return array
     */
    public function sortElementsDataProvider()
    {
        return array(
            'at_top' => array(
                'instructions' => array(
                    array('name' => 'element1'),
                    array('name' => 'element2'),
                    array('name' => 'element3', 'before' => '-')
                ),
                'expectations' => array(
                    'root' => array('element3', 'element1', 'element2')
                )
            ),
            'at_bottom' => array(
                'instructions' => array(
                    array('name' => 'element1'),
                    array('name' => 'element2', 'after' => '-'),
                    array('name' => 'element3')
                ),
                'expectations' => array(
                    'root' => array('element1', 'element3', 'element2')
                )
            ),
            'after_further_created_element' => array(
                'instructions' => array(
                    array('name' => 'element1', 'after' => 'element2'),
                    array('name' => 'element2')
                ),
                'expectations' => array(
                    'root' => array('element2', 'element1')
                )
            ),
            'before_further_created_element_deeper' => array(
                'instructions' => array(
                    array('name' => 'element1'),
                    array('name' => 'element2'),
                    array('name' => 'element2_1', 'parent' => 'element2'),
                    array('name' => 'element2_2', 'parent' => 'element2', 'before' => 'element2_4'),
                    array('name' => 'element2_3', 'parent' => 'element2'),
                    array('name' => 'element2_4', 'parent' => 'element2')
                ),
                'expectations' => array(
                    'root' => array('element1', 'element2'),
                    'element1' => array(),
                    'element2' => array('element2_1', 'element2_3', 'element2_2', 'element2_4')
                )
            ),
            'before_element_that_is_after_another_element' => array(
                'instructions' => array(
                    array('name' => 'element1', 'before' => 'element2'),
                    array('name' => 'element2', 'after' => 'element3'),
                    array('name' => 'element3', 'before' => '-')
                ),
                'expectations' => array(
                    'incomplete' => true,
                    'root' => array('element3', 'element1', 'element2')
                )
            ),
            'after_element_that_is_after_further_element' => array(
                'instructions' => array(
                    array('name' => 'element1', 'after' => 'element2'),
                    array('name' => 'element2', 'after' => 'element3'),
                    array('name' => 'element3')
                ),
                'expectations' => array(
                    'incomplete' => true,
                    'root' => array('element3', 'element2', 'element1')
                )
            ),
            'before_further_element_that_is_before_previous_element' => array(
                'instructions' => array(
                    array('name' => 'element1', 'before' => 'element3'),
                    array('name' => 'element2', 'before' => 'element1'),
                    array('name' => 'element3', 'before' => 'element_non_existing')
                ),
                'expectations' => array(
                    'incomplete' => true,
                    'root' => array('element2', 'element1', 'element3')
                )
            )
        );
    }
}
