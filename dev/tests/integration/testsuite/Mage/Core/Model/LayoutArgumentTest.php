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
 *
 * @magentoDbIsolation enabled
 */
class Mage_Core_Model_LayoutArgumentTest extends Mage_Core_Model_LayoutTestBase
{
    /**
     * @magentoConfigFixture default_store design/theme/full_name test/default
     */
    public function testLayoutArgumentsDirective()
    {
        $this->_layout->getUpdate()->load(array('layout_test_handle_arguments'));
        $this->_layout->generateXml()->generateElements();
        $this->assertEquals('1', $this->_layout->getBlock('block_with_args')->getOne());
        $this->assertEquals('two', $this->_layout->getBlock('block_with_args')->getTwo());
        $this->assertEquals('3', $this->_layout->getBlock('block_with_args')->getThree());
    }

    /**
     * @magentoConfigFixture default_store design/theme/full_name test/default
     */
    public function testLayoutArgumentsDirectiveIfComplexValues()
    {
        $this->_layout->getUpdate()->load(array('layout_test_handle_arguments_complex_values'));
        $this->_layout->generateXml()->generateElements();

        $this->assertEquals(array('parameters' => array('first' => '1', 'second' => '2')),
            $this->_layout->getBlock('block_with_args_complex_values')->getOne());

        $this->assertEquals('two', $this->_layout->getBlock('block_with_args_complex_values')->getTwo());

        $this->assertEquals(array('extra' => array('key1' => 'value1', 'key2' => 'value2')),
            $this->_layout->getBlock('block_with_args_complex_values')->getThree());
    }

    /**
     * @magentoConfigFixture default_store design/theme/full_name test/default
     */
    public function testLayoutObjectArgumentsDirective()
    {
        $this->_layout->getUpdate()->load(array('layout_test_handle_arguments_object_type'));
        $this->_layout->generateXml()->generateElements();
        $this->assertInstanceOf('Mage_Core_Block_Text', $this->_layout->getBlock('block_with_object_args')->getOne());
        $this->assertInstanceOf('Mage_Core_Block_Messages',
            $this->_layout->getBlock('block_with_object_args')->getTwo()
        );
        $this->assertEquals(3, $this->_layout->getBlock('block_with_object_args')->getThree());
    }

    /**
     * @magentoConfigFixture default_store design/theme/full_name test/default
     */
    public function testLayoutUrlArgumentsDirective()
    {
        $this->_layout->getUpdate()->load(array('layout_test_handle_arguments_url_type'));
        $this->_layout->generateXml()->generateElements();
        $this->assertContains('customer/account/login', $this->_layout->getBlock('block_with_url_args')->getOne());
        $this->assertContains('customer/account/logout', $this->_layout->getBlock('block_with_url_args')->getTwo());
        $this->assertContains('customer_id/3', $this->_layout->getBlock('block_with_url_args')->getTwo());
    }

    /**
     * @magentoConfigFixture default_store design/theme/full_name test/default
     */
    public function testLayoutObjectArgumentUpdatersDirective()
    {
        $this->_layout->getUpdate()->load(array('layout_test_handle_arguments_object_type_updaters'));
        $this->_layout->generateXml()->generateElements();

        $expectedObjectData = array(
            0 => 'updater call',
            1 => 'updater call',
            2 => 'updater call',
        );

        $expectedSimpleData = 2;

        $block = $this->_layout->getBlock('block_with_object_updater_args')->getOne();
        $this->assertInstanceOf('Mage_Core_Block_Text', $block);
        $this->assertEquals($expectedObjectData, $block->getUpdaterCall());
        $this->assertEquals($expectedSimpleData, $this->_layout->getBlock('block_with_object_updater_args')->getTwo());
    }
}
