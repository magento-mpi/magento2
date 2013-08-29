<?php
/**
 * Test format of layout files
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Layout_HandlesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $layoutFile
     * @dataProvider layoutFilesDataProvider
     */
    public function testLayoutFormat($layoutFile)
    {
        $schemaFile = __DIR__ . '/../../../../../../app/code/Mage/Core/etc/layouts.xsd';
        $domLayout = new Magento_Config_Dom(file_get_contents($layoutFile));
        $result = $domLayout->validate($schemaFile, $errors);
        $this->assertTrue($result, print_r($errors, true));
    }

    /**
     * @return array
     */
    public function layoutFilesDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getLayoutFiles();
    }
}
