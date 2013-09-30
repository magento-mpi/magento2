<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_SalesConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * attributes represent merging rules
     * copied from original class Magento_Core_Model_Route_Config_Reader
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/section' => 'name',
        '/config/section/group' => 'name',
        '/config/section/group/item' => 'name',
        '/config/section/group/item/renderer' => 'name',
        '/config/order/available_product_type' => 'name'
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
        $this->_mergedSchemaFile = $objectManager->get('Magento_Sales_Model_Config_SchemaLocator')->getSchema();
    }

    public function testSalesConfigFiles()
    {
        $invalidFiles = array();

        $files = Magento_TestFramework_Utility_Files::init()->getConfigFiles('sales.xml');
        $mergedConfig = new Magento_Config_Dom(
            '<config></config>',
            $this->_idAttributes
        );

        foreach ($files as $file) {
            $content = file_get_contents($file[0]);
            try {
                new Magento_Config_Dom($content, $this->_idAttributes);
                //merge won't be performed if file is invalid because of exception thrown
                $mergedConfig->merge($content);
            } catch (Magento_Config_Dom_ValidationException $e) {
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
