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

class Mage_Core_Model_App_EmulationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_App_Emulation
     */
    protected $_model;

    /**
     * @covers Mage_Core_Model_App_Emulation::startEnvironmentEmulation
     * @covers Mage_Core_Model_App_Emulation::stopEnvironmentEmulation
     */
    public function testEnvironmentEmulation()
    {
        $this->_model = new Mage_Core_Model_App_Emulation();
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_ADMINHTML);

        $initialEnvInfo = $this->_model->startEnvironmentEmulation(1);
        $initialDesign = $initialEnvInfo->getInitialDesign();
        $this->assertEquals(Mage_Core_Model_App_Area::AREA_ADMINHTML, $initialDesign['area']);
        $this->assertEquals(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage::getDesign()->getArea());

        $this->_model->stopEnvironmentEmulation($initialEnvInfo);
        $this->assertEquals(Mage_Core_Model_App_Area::AREA_ADMINHTML, Mage::getDesign()->getArea());
    }
}
