<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_CrontabConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * attributes represent merging rules
     * copied from original class Magento_Core_Model_Route_Config_Reader
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/job' => 'name'
    );

    /**
     * Path to tough XSD for merged file validation
     *
     * @var string
     */
    protected $_mergedSchemaFile;

    protected function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_mergedSchemaFile = $objectManager->get('Magento\Cron\Model\Config\SchemaLocator')->getSchema();
    }

    public function testCrontabConfigFiles()
    {
        $invalidFiles = array();

        $files = Magento_TestFramework_Utility_Files::init()->getConfigFiles('crontab.xml');
        $mergedConfig = new \Magento\Config\Dom(
            '<config></config>',
            $this->_idAttributes
        );

        foreach ($files as $file) {
            $content = file_get_contents($file[0]);
            try {
                new \Magento\Config\Dom($content, $this->_idAttributes);
                //merge won't be performed if file is invalid because of exception thrown
                $mergedConfig->merge($content);
            } catch (\Magento\Config\Dom\ValidationException $e) {
                $invalidFiles[] = $file[0];
            }
        }

        if (!empty($invalidFiles)) {
            $this->fail('Found broken files: ' . implode("\n", $invalidFiles));
        }

        $errors = array();
        $mergedConfig->validate($this->_mergedSchemaFile, $errors);
        if ($errors) {
            $this->fail('Merged routes config is invalid: ' . "\n" . implode("\n", $errors));
        }
    }
}
