<?php
/**
 * Tests that existing widget.xml files are valid to schema individually and merged.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_Modular_WidgetConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    /**
     * @var  Magento_Widget_Model_Config_Reader
     */
    protected $_reader;

    protected $_fileResolverMock;
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $widgetFiles = $this->getWidgetConfigFiles();
        if (!empty($widgetFiles)) {

            $this->_fileResolverMock = $this->getMockBuilder('Magento_Core_Model_Config_FileResolver_Primary')
                ->disableOriginalConstructor()->getMock();

            $this->_reader = $this->_objectManager->create('Magento_Widget_Model_Config_Reader', array(
                'configFiles' => $widgetFiles, 'fileResolver' => $this->_fileResolverMock));

            /** @var $dirs Magento_Core_Model_Dir */
            $dirs = $this->_objectManager->get('Magento_Core_Model_Dir');
            $modulesDir = $dirs->getDir(Magento_Core_Model_Dir::MODULES);
            $this->_schemaFile = $modulesDir . '/Magento/Widget/etc/widget_file.xsd';
        }
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Magento_Widget_Model_Config_Reader');
    }

    public function getWidgetConfigFiles()
    {
        return glob(Mage::getBaseDir('app') . '/*/*/*/etc/widget.xml');
    }

    public function widgetConfigFilesProvider()
    {
        $fileList = $this->getWidgetConfigFiles();
        if (empty($fileList)) {
            return array(array(false, true));
        }

        $dataProviderResult = array();
        foreach ($fileList as $file) {
            $dataProviderResult[$file] = array($file);
        }
        return $dataProviderResult;
    }

    /**
     * @dataProvider widgetConfigFilesProvider
     */
    public function testWidgetConfigFile($file, $skip = false)
    {
        if ($skip) {
            $this->markTestSkipped('There are no widget.xml files in the system');
        }
        $domConfig = new Magento_Config_Dom(file_get_contents($file));
        $result = $domConfig->validate($this->_schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "$error\n";
        }

        $this->assertTrue($result, $message);
    }

    public function testMergedConfig()
    {
        // have the file resolver return all widget.xml files
        $this->_fileResolverMock->expects($this->once())->method('get')
            ->will($this->returnValue($this->getWidgetConfigFiles()));
        try {
            // this will merge all xml files and validate them
            $this->_reader->read('global');
        } catch (Magento_Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
