<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Integrity_Modular_ViewConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider viewConfigFileDataProvider
     */
    public function testViewConfigFile($file)
    {
        $domConfig = new Magento_Config_Dom(file_get_contents($file));
        $result = $domConfig->validate(Mage::getBaseDir('lib') . '/Magento/Config/etc/view.xsd', $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function viewConfigFileDataProvider()
    {
        $result = array();
        $files = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Config_Modules_Reader')
            ->getConfigurationFiles('view.xml');
        foreach ($files as $file) {
            $result[] = array($file);
        }
        return $result;
    }
}
