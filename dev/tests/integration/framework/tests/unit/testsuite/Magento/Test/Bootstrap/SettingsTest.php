<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Test_Bootstrap_Settings.
 */
class Magento_Test_Bootstrap_SettingsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Bootstrap_Settings
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new Magento_Test_Bootstrap_Settings(__DIR__, array(
            'item_label'                => 'Item Label',
            'number_of_items'           => 42,
            'item_price'                => 12.99,
            'is_in_stock'               => true,
            'free_shipping'             => 'enabled',
            'test_file'                 => basename(__FILE__),
            'all_xml_files'             => '_files/*.xml',
            'all_xml_or_one_php_file'   => '_files/{*.xml,4.php}',
            'one_xml_or_any_php_file'   => '_files/1.xml;_files/?.php',
            'config_file_with_dist'     => '_files/1.xml',
            'config_file_no_dist'       => '_files/2.xml',
            'no_config_file_dist'       => '_files/3.xml',
        ));
    }

    protected function tearDown()
    {
        $this->_object = null;
    }

    /**
     * @param string $settingName
     * @param mixed $defaultValue
     * @param mixed $expectedResult
     * @dataProvider getScalarValueDataProvider
     */
    public function testGetScalarValue($settingName, $defaultValue, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->_object->getScalarValue($settingName, $defaultValue));
    }

    public function getScalarValueDataProvider()
    {
        return array(
            'string type'   => array('item_label', null, 'Item Label'),
            'integer type'  => array('number_of_items', null, 42),
            'float type'    => array('item_price', null, 12.99),
            'boolean type'  => array('is_in_stock', null, true),
            'non-existing'  => array('non_existing', null, null),
            'default value' => array('non_existing', 'default', 'default'),
        );
    }

    /**
     * @param string $settingName
     * @param bool $expectedResult
     * @dataProvider isEnabledDataProvider
     */
    public function testIsEnabled($settingName, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->_object->isEnabled($settingName));
    }

    public function isEnabledDataProvider()
    {
        return array(
            'non-enabled string'    => array('item_label', false),
            'non-enabled boolean'   => array('is_in_stock', false),
            'enabled string'        => array('free_shipping', true),
        );
    }

    /**
     * @param string $settingName
     * @param mixed $defaultValue
     * @param string $expectedResult
     * @dataProvider getFileValueDataProvider
     */
    public function testGetFileValue($settingName, $defaultValue, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->_object->getFileValue($settingName, $defaultValue));
    }

    public function getFileValueDataProvider()
    {
        return array(
            'existing file'     => array('test_file', null, __FILE__),
            'non-existing file' => array('non_existing_file', null, null),
            'default value'     => array('non_existing_file', basename(__FILE__), __FILE__),
        );
    }

    /**
     * @param string $settingName
     * @param mixed $defaultValue
     * @param string $expectedResult
     * @dataProvider getPathPatternValueDataProvider
     */
    public function testGetPathPatternValue($settingName, $defaultValue, $expectedResult)
    {
        $actualResult = $this->_object->getPathPatternValue($settingName, $defaultValue);
        if (is_array($actualResult)) {
            sort($actualResult);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function getPathPatternValueDataProvider()
    {
        $fixtureDir = __DIR__ . DIRECTORY_SEPARATOR . '_files';
        return array(
            'single pattern' => array(
                'all_xml_files', null, array("$fixtureDir/1.xml", "$fixtureDir/2.xml")
            ),
            'pattern with braces' => array(
                'all_xml_or_one_php_file', null, array("$fixtureDir/1.xml", "$fixtureDir/2.xml", "$fixtureDir/4.php")
            ),
            'multiple patterns' => array(
                'one_xml_or_any_php_file', null, array("$fixtureDir/1.xml", "$fixtureDir/4.php")
            ),
            'non-existing setting' => array(
                'non_existing', null, null
            ),
            'default value' => array(
                'non_existing', '_files/2.xml;_files/4.php', array("$fixtureDir/2.xml", "$fixtureDir/4.php")
            ),
        );
    }

    /**
     * @param string $settingName
     * @param mixed $defaultValue
     * @param array $extraConfigFiles
     * @param mixed $expectedResult
     * @dataProvider getConfigFilesDataProvider
     */
    public function testGetConfigFiles($settingName, $defaultValue, array $extraConfigFiles, $expectedResult)
    {
        $actualResult = $this->_object->getConfigFiles($settingName, $defaultValue, $extraConfigFiles);
        if (is_array($actualResult)) {
            sort($actualResult);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function getConfigFilesDataProvider()
    {
        $fixtureDir = __DIR__ . DIRECTORY_SEPARATOR . '_files';
        return array(
            'config file & dist file' => array(
                'config_file_with_dist', null, array(), array("$fixtureDir/1.xml")
            ),
            'config file & no dist file' => array(
                'config_file_no_dist', null, array(), array("$fixtureDir/2.xml")
            ),
            'no config file & dist file' => array(
                'no_config_file_dist', null, array(), array("$fixtureDir/3.xml.dist")
            ),
            'default value' => array(
                'non_existing', '_files/1.xml', array(), array("$fixtureDir/1.xml")
            ),
            'extra config files' => array(
                'config_file_with_dist', null, array('_files/2.xml'), array("$fixtureDir/1.xml", "$fixtureDir/2.xml")
            ),
        );
    }
}
