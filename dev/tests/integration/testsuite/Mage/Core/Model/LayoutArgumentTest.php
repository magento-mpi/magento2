<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout integration tests
 */
class Mage_Core_Model_LayoutArgumentTest extends Mage_Core_Model_LayoutTestBase
{

    public function testLayoutArgumentsDirective()
    {
        $layout = new Mage_Core_Model_Layout();
        $layout->getUpdate()->load(array('layout_test_handle_arguments'));
        $layout->generateXml()->generateElements();
        $this->assertEquals('1', $layout->getBlock('block_with_args')->getOne());
        $this->assertEquals('two', $layout->getBlock('block_with_args')->getTwo());
        $this->assertEquals('3', $layout->getBlock('block_with_args')->getThree());
    }

    public function testLayoutArgumentsDirectiveIfComplexValues()
    {
        $layout = new Mage_Core_Model_Layout();
        $layout->getUpdate()->load(array('layout_test_handle_arguments_complex_values'));
        $layout->generateXml()->generateElements();

        $this->assertEquals(array('parameters' => array('first' => '1', 'second' => '2')),
            $layout->getBlock('block_with_args_complex_values')->getOne());

        $this->assertEquals('two', $layout->getBlock('block_with_args_complex_values')->getTwo());

        $this->assertEquals(array('extra' => array('key1' => 'value1', 'key2' => 'value2')),
            $layout->getBlock('block_with_args_complex_values')->getThree());
    }

    public function testLayoutObjectArgumentsDirective()
    {
        $layout = new Mage_Core_Model_Layout();
        $layout->getUpdate()->load(array('layout_test_handle_arguments_object_type'));
        $layout->generateXml()->generateElements();
        $this->assertInstanceOf('Mage_Core_Block_Text', $layout->getBlock('block_with_object_args')->getOne());
        $this->assertInstanceOf('Mage_Core_Block_Messages', $layout->getBlock('block_with_object_args')->getTwo());
        $this->assertEquals(3, $layout->getBlock('block_with_object_args')->getThree());
    }

    public function testLayoutUrlArgumentsDirective()
    {
        $layout = new Mage_Core_Model_Layout();
        $layout->getUpdate()->load(array('layout_test_handle_arguments_url_type'));
        $layout->generateXml()->generateElements();
        $this->assertContains('customer/account/login', $layout->getBlock('block_with_url_args')->getOne());
        $this->assertContains('customer/account/logout', $layout->getBlock('block_with_url_args')->getTwo());
        $this->assertContains('customer_id/3', $layout->getBlock('block_with_url_args')->getTwo());
    }

    public function testLayoutObjectArgumentUpdatersDirective()
    {
        $layout = new Mage_Core_Model_Layout();
        $layout->getUpdate()->load(array('layout_test_handle_arguments_object_type_updaters'));
        $layout->generateXml()->generateElements();

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
}
