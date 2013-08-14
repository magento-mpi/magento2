<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_App_EmulationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_App_Emulation
     */
    protected $_model;

    /**
     * @covers Magento_Core_Model_App_Emulation::startEnvironmentEmulation
     * @covers Magento_Core_Model_App_Emulation::stopEnvironmentEmulation
     */
    public function testEnvironmentEmulation()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_App_Emulation');
        Mage::getDesign()->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML);

        $initialEnvInfo = $this->_model->startEnvironmentEmulation(1);
        $initialDesign = $initialEnvInfo->getInitialDesign();
        $this->assertEquals(Magento_Core_Model_App_Area::AREA_ADMINHTML, $initialDesign['area']);
        $this->assertEquals(Magento_Core_Model_App_Area::AREA_FRONTEND, Mage::getDesign()->getArea());

        $this->_model->stopEnvironmentEmulation($initialEnvInfo);
        $this->assertEquals(Magento_Core_Model_App_Area::AREA_ADMINHTML, Mage::getDesign()->getArea());
    }
}
