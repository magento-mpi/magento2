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
     *
     * @magentoAppIsolation enabled
     */
    public function testEnvironmentEmulation()
    {
        $this->_model = new Mage_Core_Model_App_Emulation();
        Mage::getDesign()->setArea('adminhtml');

        $initialEnvInfo = $this->_model->startEnvironmentEmulation(1);
        $initialDesign = $initialEnvInfo->getInitialDesign();
        $this->assertEquals('adminhtml', $initialDesign['area']);
        $this->assertEquals('frontend', Mage::getDesign()->getArea());

        $this->_model->stopEnvironmentEmulation($initialEnvInfo);
        $this->assertEquals('adminhtml', Mage::getDesign()->getArea());
    }
}
