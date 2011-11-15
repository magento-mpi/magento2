<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Customer
 */
class Mage_Customer_Model_Convert_Adapter_CustomerTest extends PHPUnit_Framework_TestCase
{
    public function testLoadWithoutIssues()
    {
        $model = $this->getMock('Mage_Customer_Model_Convert_Adapter_Customer', array('addException'));

        $exceptionWas = false;
        $checkException = function ($error, $level = null) use (&$exceptionWas) {
            if ($level == Varien_Convert_Exception::FATAL) {
                $exceptionWas = true;
            }
            return new Mage_Dataflow_Model_Convert_Exception($error);
        };

        $model->expects($this->any())
            ->method('addException')
            ->will($this->returnCallback($checkException));
        $model->load();

        $this->assertFalse($exceptionWas, 'Exception happened during loading');
    }
}
