<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Export_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_ImportExport_Helper_Export_File
     */
    protected $_helperModel;

    public function setUp() {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_helperModel = $objectManager->getObject('Stub_Helper_Export_File');
    }

    public function testGetDownloadName()
    {
        $this->assertEquals(Stub_Helper_Export_File::FILE_DOWNLOAD_NAME, $this->_helperModel->getDownloadName());
    }

    public function testGetPath()
    {
        $this->assertEquals(Stub_Helper_Export_File::FILE_PATH, $this->_helperModel->getPath());
    }

    public function testGetMimeType()
    {
        $this->assertEquals(Saas_ImportExport_Helper_Export_File::MIME_TYPE_DEFAULT,
            $this->_helperModel->getMimeType());

        $this->_setFileProperty(array('extension' => Saas_ImportExport_Model_Export_Adapter_Csv::EXTENSION_CSV));
        $this->assertEquals(Saas_ImportExport_Helper_Export_File::MIME_TYPE_CSV,
            $this->_helperModel->getMimeType());
    }

    /**
     * Is export file exist
     *
     * @return bool
     */
    public function testIsExist()
    {
        $this->assertTrue($this->_helperModel->isExist());
        $this->_setFileProperty(null);
        $this->assertFalse($this->_helperModel->isExist());
    }

    /**
     * Set $_file property value
     *
     * @param mixed $value
     */
    protected function _setFileProperty($value)
    {
        $class = new ReflectionClass('Stub_Helper_Export_File');
        $property = $class->getProperty('_file');
        $property->setAccessible(true);
        $property->setValue($this->_helperModel, $value);
    }
}

/**
 * Stub class for Saas_ImportExport_Helper_Export_File. Init $_file property
 */
class Stub_Helper_Export_File extends Saas_ImportExport_Helper_Export_File
{
    /**#@+
     * File parameters
     */
    const FILE_DOWNLOAD_NAME = 'some-name';
    const FILE_PATH = 'some-path';
    const FILE_EXTENSION = 'some-extension';
    /**#@-*/

    public function __construct(
        Mage_Core_Helper_Context $context,
        Saas_ImportExport_Helper_Export_Config $configHelper,
        Saas_ImportExport_Model_Export_State_Flag $flagFactory,
        Magento_Filesystem $filesystem
    ) {
        parent::__construct($context, $configHelper, $flagFactory, $filesystem);
        $this->_file = array(
            'download_name' => self::FILE_DOWNLOAD_NAME, 'path' => self::FILE_PATH, 'extension' => self::FILE_EXTENSION
        );
    }
}
