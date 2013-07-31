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
 * Test class for Mage_Core_Model_Source_Urlrewrite_Types.
 */
class Mage_Core_Model_Source_Urlrewrite_TypesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Initialize helper
     */
    protected function setUp()
    {
        $helper = $this->getMockBuilder('Magento_Adminhtml_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));
        Mage::register('_helper/Magento_Adminhtml_Helper_Data', $helper);
    }

    /**
     * Clear helper
     */
    protected function tearDown()
    {
        Mage::unregister('_helper/Magento_Adminhtml_Helper_Data');
    }

    /**
     * @covers Mage_Core_Model_Source_Urlrewrite_Types::getAllOptions
     */
    public function testGetAllOptions()
    {
        $model = new Mage_Core_Model_Source_Urlrewrite_Types();
        $options = $model->getAllOptions();
        $this->assertInternalType('array', $options);
        $expectedOptions = array(
            1 => 'System',
            0 => 'Custom'
        );
        $this->assertEquals($expectedOptions, $options);
    }
}
