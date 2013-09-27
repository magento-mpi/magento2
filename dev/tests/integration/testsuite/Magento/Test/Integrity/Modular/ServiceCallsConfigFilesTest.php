<?php
/**
 * Tests that existing service_calls.xml files are valid to schema.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Modular;

class ServiceCallsConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    /**
     * @var  \Magento\Core\Model\DataService\Config\Reader
     */
    protected $_reader;

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $serviceCallsFiles = $this->getServiceCallsConfigFiles();
        if (!empty($serviceCallsFiles)) {
            $this->_reader = $this->_objectManager->create('Magento\Core\Model\DataService\Config\Reader', array(
                'configFiles' => $serviceCallsFiles));
            $this->_schemaFile = $this->_reader->getSchemaFile();
        }
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Magento\Core\Model\DataService\Config\Reader');
    }

    public function getServiceCallsConfigFiles()
    {
        return glob(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Dir')->getDir('app')
                . '/*/*/*/etc/service_calls.xml'
        );
    }

    public function serviceCallsConfigFilesProvider()
    {
        $fileList = $this->getServiceCallsConfigFiles();
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
     * @dataProvider serviceCallsConfigFilesProvider
     */
    public function testServiceCallsConfigFile($file, $skip = false)
    {
        if ($skip) {
            $this->markTestSkipped('There is no service_calls.xml files in the system');
        }
        $domConfig = new \Magento\Config\Dom(file_get_contents($file));
        $result = $domConfig->validate($this->_schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "$error\n";
        }

        $this->assertTrue($result, $message);
    }

    public function testMergedConfig()
    {
        if (is_null($this->_reader)) {
            $this->markTestSkipped('There is no service_calls.xml files in the system');
            return;
        }

        try {
            $this->_reader->validate();
        } catch (\Magento\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
