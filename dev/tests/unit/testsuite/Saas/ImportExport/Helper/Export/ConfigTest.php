<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Export_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirModel;

    /**
     * @var Saas_ImportExport_Helper_Export_Config
     */
    protected $_helperModel;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_dirModel = $objectManager->getObject('Mage_Core_Model_Dir');
        $this->_helperModel = $objectManager->getObject('Saas_ImportExport_Helper_Export_Config', array(
            'context' => $this->getMock('Mage_Core_Helper_Context', array(), array(), '', false),
            'applicationConfig' => $this->_configMock,
            'dir' => $this->_dirModel
        ));
    }

    /**
     * @param string $entityType
     * @param int $itemsPerPage
     * @dataProvider dataProviderForGetItemsPerPage
     */
    public function testGetItemsPerPage($entityType, $itemsPerPage)
    {
        $this->_configMock->expects($this->once())->method('getNode')->will($this->returnValue($itemsPerPage));
        $this->assertEquals($itemsPerPage, $this->_helperModel->getItemsPerPage($entityType));
    }

    /**
     * @return array
     */
    public function dataProviderForGetItemsPerPage()
    {
        return array(
            array('catalog_product', 100),
            array('customer', 10),
        );
    }

    /**
     * @param string $entityType
     * @dataProvider dataProviderForStorageFilePath
     */
    public function testGetStorageFilePath($entityType)
    {
        $this->assertEquals($this->_dirModel->getDir('media')
            . Magento_Filesystem::DIRECTORY_SEPARATOR . 'importexport'
            . Magento_Filesystem::DIRECTORY_SEPARATOR . 'export'
            . Magento_Filesystem::DIRECTORY_SEPARATOR . $entityType,
            $this->_helperModel->getStorageFilePath($entityType));
    }

    /**
     * @return array
     */
    public function dataProviderForStorageFilePath()
    {
        return array(
            array('catalog_product'),
            array('customer'),
        );
    }
}
