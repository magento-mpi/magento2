<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Backend_Image_AdapterTest extends Mage_Backend_Area_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Backend_Image_Adapter
     */
    protected $_model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('Mage_Backend_Model_Config_Backend_Image_Adapter');
    }

    /**
     * @expectedException Mage_Core_Exception
     * expectedExceptionMessage  The specified image adapter cannot be used because of some missed dependencies.
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExceptionSave()
    {
        $this->_model->setValue('wrong')->save();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCorrectSave()
    {
        $this->_model->setValue(Mage_Core_Model_Image_AdapterFactory::ADAPTER_GD2)->save();
    }
}
