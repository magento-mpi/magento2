<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_Modular_CrontabConfigFilesTest extends PHPUnit_Framework_TestCase
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

    public function setUp()
    {
        global $magentoBaseDir;

        $this->_mergedSchemaFile = $magentoBaseDir . '/app/code/Magento/Cron/etc/crontab.xsd';
    }

    public function testCrontabConfigsValidation()
    {
        global $magentoBaseDir;
        $invalidFiles = array();

        $mask = $magentoBaseDir . '/app/code/*/*/etc/crontab.xml';
        $files = glob($mask);
        $mergedConfig = new Magento_Config_Dom(
            '<config></config>',
            $this->_idAttributes
        );

        foreach ($files as $file) {
            $content = file_get_contents($file);
            try {
                new Magento_Config_Dom($content, $this->_idAttributes);
                //merge won't be performed if file is invalid because of exception thrown
                $mergedConfig->merge($content);
            } catch (Magento_Config_Dom_ValidationException $e) {
                $invalidFiles[] = $file;
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
