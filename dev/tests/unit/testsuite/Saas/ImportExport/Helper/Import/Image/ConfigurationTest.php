<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Import_Image_ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * @var Saas_ImportExport_Helper_Import_Image_Configuration
     */
    protected $_helper;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_helper = $objectManager->getObject('Saas_ImportExport_Helper_Import_Image_Configuration', array(
            'config' => $this->_configMock,
            'dir' => $this->_dirMock,
        ));
    }

    /**
     * @param string $method
     * @param string $path
     * @dataProvider dataProviderForTestGetConfigStringValue
     */
    public function testGetConfigStringValue($method, $path)
    {
        $value = 'some-value';
        $this->_configMock->expects($this->once())->method('getNode')->with($path)->will($this->returnValue($value));

        $this->assertEquals($value, $this->_helper->$method());
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetConfigStringValue()
    {
        return array(
            array('getTypeCode', Saas_ImportExport_Helper_Import_Image_Configuration::XML_PATH_TYPE_CODE),
            array('getFileFieldName',
                Saas_ImportExport_Helper_Import_Image_Configuration::XML_PATH_FIELD_ARCHIVE_FILE_NAME),
        );
    }

    /**
     * @param string $method
     * @param string $path
     * @dataProvider dataProviderForTestGetConfigIntegerValue
     */
    public function testGetConfigIntegerValue($method, $path)
    {
        $value = 1235;
        $this->_configMock->expects($this->once())->method('getNode')->with($path)->will($this->returnValue($value));

        $this->assertEquals($value, $this->_helper->$method());
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetConfigIntegerValue()
    {
        return array(
            array('getImageFilenameLimit',
                Saas_ImportExport_Helper_Import_Image_Configuration::XML_PATH_IMAGE_FILENAME_LIMIT),
            array('getImageWidthLimit',
                Saas_ImportExport_Helper_Import_Image_Configuration::XML_PATH_IMAGE_WIDTH_LIMIT),
            array('getImageHeightLimit',
                Saas_ImportExport_Helper_Import_Image_Configuration::XML_PATH_IMAGE_HEIGHT_LIMIT),
            array('getImageFileSizeLimit',
                Saas_ImportExport_Helper_Import_Image_Configuration::XML_PATH_IMAGE_FILE_SIZE_LIMIT),
        );
    }

    /**
     * @param string $method
     * @param string $path
     * @param bool $useKey
     * @dataProvider dataProviderForTestGetConfigArrayValue
     */
    public function testGetConfigArrayValue($method, $path, $useKey)
    {
        $data = array('key1' => 'test1', 'key2' => 'test2', 'key3' => 'test3');

        // Use stdObject instead of Mage_Core_Model_Config_Element because simplexml object has not empty __wakeUp
        $configElementMock = $this->getMock('stdObject', array('asArray'), array(), '', false);
        $configElementMock->expects($this->once())->method('asArray')
            ->will($this->returnValue($data));

        $this->_configMock->expects($this->once())->method('getNode')->with($path)
            ->will($this->returnValue($configElementMock));

        $expected = $useKey ? array_keys($data) : array_values($data);
        $this->assertEquals($expected, $this->_helper->$method());
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetConfigArrayValue()
    {
        return array(
            array('getArchiveAllowedExtensions',
                Saas_ImportExport_Helper_Import_Image_Configuration::XML_PATH_ARCHIVE_ALLOWED_EXTENSIONS, true),
            array('getImageAllowedExtensions',
                Saas_ImportExport_Helper_Import_Image_Configuration::XML_PATH_IMAGE_ALLOWED_EXTENSIONS, true),
            array('getImageAllowedMimetypes',
                Saas_ImportExport_Helper_Import_Image_Configuration::XML_PATH_IMAGE_ALLOWED_MIMETYPES, false),
        );
    }

    public function testGetWorkingDir()
    {
        $dirVar = 'var';
        $this->_dirMock->expects($this->once())->method('getDir')->with('var')->will($this->returnValue($dirVar));

        $this->assertEquals($dirVar . DS . 'importexport' . DS . 'images' . DS, $this->_helper->getWorkingDir());
    }

    public function testGetWorkingUnZipDir()
    {
        $dirVar = 'var';
        $this->_dirMock->expects($this->once())->method('getDir')->with('var')->will($this->returnValue($dirVar));

        $this->assertEquals(
            $dirVar . DS . 'importexport' . DS . 'images' . DS . 'unzip' . DS,
            $this->_helper->getWorkingUnZipDir()
        );
    }
}
