<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Model_Url
 */
class Mage_Backend_Model_Config_MenuTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test existence of xsd file
     */
    public function testGetSchemaFile()
    {
        $fileName = realpath(__DIR__)  . '/../_files/menu.xml';
        $model = $this->getMockForAbstractClass(
            'Mage_Backend_Model_Config_Menu',
            array(),
            '',
            false
        );
        $actual = $model->getSchemaFile();
        $this->assertFileExists($actual, 'XSD file [' . $actual . '] not exist');
    }

    /**
     * Test output data type of method getMergedConfig
     */
    public function testGetMergedConfigDataType()
    {
        $model = $this->getMockForAbstractClass(
            'Mage_Backend_Model_Config_Menu',
            array(),
            '',
            false
        );
        $this->assertInstanceOf('DOMDocument', $model->getMergedConfig(), 'Invalid output type');
    }

    /**
     * Test output data type of method getMergedConfig
     */
    public function testGetMergedConfig()
    {
        $basePath = realpath(__DIR__)  . '/../_files/';

        $expectedFile = $basePath . 'menu_merged.xml';
        $files = array(
            $basePath . 'menu_1.xml',
            $basePath . 'menu_2.xml',
        );
        $model = $this->getMockForAbstractClass(
            'Mage_Backend_Model_Config_Menu',
            array($files)
        );
        $actual = $model->getMergedConfig();
        $actual->preserveWhiteSpace = false;

        $this->assertInstanceOf('DOMDocument', $actual, 'Invalid output type');
        $expected = new DOMDocument();
        $expected->preserveWhiteSpace = false;
        $expected->load($expectedFile);
        $this->assertEqualXMLStructure(
            $expected->documentElement,
            $actual->documentElement,
            true,
            'Incorrect document structure'
        );
        $this->assertEquals($expected, $actual, 'Incorrect configuration merge');
    }
}
