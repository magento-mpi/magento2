<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_XmlConnect
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_XmlConnect_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_XmlConnect_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::helper('Mage_XmlConnect_Helper_Data');
        Mage::getDesign()->setDesignTheme('default/basic', 'adminhtml');
    }

    protected function tearDown()
    {
        $this->_helper = null;
    }

    /**
     * @dataProvider getDefaultDesignTabsDataProvider
     */
    public function testGetDefaultDesignTabs($appType)
    {
        $application = Mage::getModel('Mage_XmlConnect_Model_Application');
        $application->setType($appType);
        $tabs = $this->_helper->getDeviceHelper($application)->getDefaultDesignTabs();
        $this->assertNotEmpty($tabs);
        foreach ($tabs as $tab) {
            $this->assertArrayHasKey('image', $tab);
        }
    }

    /**
     * @return array
     */
    public function getDefaultDesignTabsDataProvider()
    {
        return array(
            array(Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID),
            array(Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD),
            array(Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE)
        );
    }
}
