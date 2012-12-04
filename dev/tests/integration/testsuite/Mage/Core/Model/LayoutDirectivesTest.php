<?php
/**
 * Set of tests of layout directives handling behavior
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_LayoutDirectivesTest extends PHPUnit_Framework_TestCase
{
    public function testLayoutArgumentsDirective()
    {
        $layout = $this->_getLayoutModel('arguments.xml');
        $this->assertEquals('1', $layout->getBlock('block_with_args')->getOne());
        $this->assertEquals('two', $layout->getBlock('block_with_args')->getTwo());
        $this->assertEquals('3', $layout->getBlock('block_with_args')->getThree());
    }

    public function testLayoutArgumentsDirectiveIfComplexValues()
    {
        $layout = $this->_getLayoutModel('arguments_complex_values.xml');

        $this->assertEquals(array('parameters' => array('first' => '1', 'second' => '2')),
            $layout->getBlock('block_with_args_complex_values')->getOne());

        $this->assertEquals('two', $layout->getBlock('block_with_args_complex_values')->getTwo());

        $this->assertEquals(array('extra' => array('key1' => 'value1', 'key2' => 'value2')),
            $layout->getBlock('block_with_args_complex_values')->getThree());
    }

    public function testLayoutObjectArgumentsDirective()
    {
        $layout = $this->_getLayoutModel('arguments_object_type.xml');
        $this->assertInstanceOf('Mage_Core_Block_Text', $layout->getBlock('block_with_object_args')->getOne());
        $this->assertInstanceOf('Mage_Core_Block_Messages',
            $layout->getBlock('block_with_object_args')->getTwo()
        );
        $this->assertEquals(3, $layout->getBlock('block_with_object_args')->getThree());
    }

    public function testLayoutUrlArgumentsDirective()
    {
        $layout = $this->_getLayoutModel('arguments_url_type.xml');
        $this->assertContains('customer/account/login', $layout->getBlock('block_with_url_args')->getOne());
        $this->assertContains('customer/account/logout', $layout->getBlock('block_with_url_args')->getTwo());
        $this->assertContains('customer_id/3', $layout->getBlock('block_with_url_args')->getTwo());
    }

    public function testLayoutObjectArgumentUpdatersDirective()
    {
        $layout = $this->_getLayoutModel('arguments_object_type_updaters.xml');

        $expectedObjectData = array(
            0 => 'updater call',
            1 => 'updater call',
            2 => 'updater call',
        );

        $expectedSimpleData = 2;

        $block = $layout->getBlock('block_with_object_updater_args')->getOne();
        $this->assertInstanceOf('Mage_Core_Block_Text', $block);
        $this->assertEquals($expectedObjectData, $block->getUpdaterCall());
        $this->assertEquals($expectedSimpleData, $layout->getBlock('block_with_object_updater_args')->getTwo());
    }

    public function testMoveSameAlias()
    {
        $layout = $this->_getLayoutModel('move_the_same_alias.xml');
        $this->assertEquals('container1', $layout->getParentName('no_name3'));
    }

    public function testMoveNewAlias()
    {
        $layout = $this->_getLayoutModel('move_new_alias.xml');
        $this->assertEquals('new_alias', $layout->getElementAlias('no_name3'));
    }

    public function testActionAnonymousParentBlock()
    {
        $layout = $this->_getLayoutModel('action_for_anonymous_parent_block.xml');
        $this->assertEquals('schedule_block', $layout->getParentName('test.block.insert'));
        $this->assertEquals('schedule_block_1', $layout->getParentName('test.block.append'));
    }

    public function testRemove()
    {
        $layout = $this->_getLayoutModel('remove.xml');
        $this->assertFalse($layout->getBlock('no_name2'));
        $this->assertFalse($layout->getBlock('child_block1'));
        $this->assertTrue($layout->isBlock('child_block2'));
    }

    public function testMove()
    {
        $layout = $this->_getLayoutModel('move.xml');
        $this->assertEquals('container2', $layout->getParentName('container1'));
        $this->assertEquals('container1', $layout->getParentName('no.name2'));
        $this->assertEquals('block_container', $layout->getParentName('no_name3'));

        // verify `after` attribute
        $this->assertEquals('block_container', $layout->getParentName('no_name'));
        $childrenOrderArray = array_keys($layout->getChildBlocks($layout->getParentName('no_name')));
        $positionAfter = array_search('child_block1', $childrenOrderArray);
        $positionToVerify = array_search('no_name', $childrenOrderArray);
        $this->assertEquals($positionAfter, --$positionToVerify);

        // verify `before` attribute
        $this->assertEquals('block_container', $layout->getParentName('no_name4'));
        $childrenOrderArray = array_keys($layout->getChildBlocks($layout->getParentName('no_name4')));
        $positionBefore = array_search('child_block2', $childrenOrderArray);
        $positionToVerify = array_search('no_name4', $childrenOrderArray);
        $this->assertEquals($positionBefore, ++$positionToVerify);
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testMoveBroken()
    {
        $this->_getLayoutModel('move_broken.xml');
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testMoveAliasBroken()
    {
        $this->_getLayoutModel('move_alias_broken.xml');
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testRemoveBroken()
    {
        $this->_getLayoutModel('remove_broken.xml');
    }

    /**
     * Prepare a layout model with pre-loaded fixture of an update XML
     *
     * @param string $fixtureFile
     * @return Mage_Core_Model_Layout
     */
    protected function _getLayoutModel($fixtureFile)
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $layout->setXml(simplexml_load_file(
            __DIR__ . "/_files/layout_directives_test/{$fixtureFile}",
            'Mage_Core_Model_Layout_Element'
        ));
        $layout->generateElements();
        return $layout;
    }
}
