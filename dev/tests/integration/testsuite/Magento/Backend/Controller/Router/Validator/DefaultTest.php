<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class Magento_Backend_Controller_Router_Default
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Controller_Router_Validator_DefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture global/areas/adminhtml/frontName 0
     * @expectedException InvalidArgumentException
     * @magentoAppIsolation enabled
     */
    public function testConstructWithEmptyAreaFrontName()
    {
        $dataHelperMock = $this->getMock('Magento_Backend_Helper_Data', array(), array(), '', false);
        $dataHelperMock->expects($this->once())->method('getAreaFrontName')->will($this->returnValue(null));

        $options = array(
            'areaCode' => Magento_Core_Model_App_Area::AREA_ADMINHTML,
            'baseController' => 'Magento_Backend_Controller_ActionAbstract',
            'backendData' => $dataHelperMock,
        );
        Mage::getModel('Magento_Backend_Controller_Router_Default', $options);
    }

    /**
     * @magentoConfigFixture global/areas/adminhtml/frontName backend
     * @magentoAppIsolation enabled
     */
    public function testConstructWithNotEmptyAreaFrontName()
    {
        $options = array(
            'areaCode'       => Magento_Core_Model_App_Area::AREA_ADMINHTML,
            'baseController' => 'Magento_Backend_Controller_ActionAbstract',
        );
        Mage::getModel('Magento_Backend_Controller_Router_Default', $options);
    }
}
