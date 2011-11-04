<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Catalog
 */
class Mage_Catalog_Model_Convert_Adapter_ProductTest extends PHPUnit_Framework_TestCase
{
    public function testLoadWithoutIssues()
    {
        $model = $this->getMock('Mage_Catalog_Model_Convert_Adapter_Product', array('addException'));

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
