<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_DesignEditor_Model_Editor_Tools_Controls_ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_DesignEditor_Model_Editor_Tools_Controls_Factory
     */
    protected $_configFactory;

    /**
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_design;

    /**
     * Initialize dependencies
     */
    protected function setUp()
    {
        $this->_design = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get(
            'Magento_Core_Model_View_DesignInterface'
        );
        $this->_design->setDesignTheme('vendor_test_child', Magento_Core_Model_View_DesignInterface::DEFAULT_AREA);
        $this->_configFactory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create(
            'Magento_DesignEditor_Model_Editor_Tools_Controls_Factory'
        );
    }

    /**
     * Test control data
     *
     * @magentoDataFixture Magento/DesignEditor/Model/_files/design/themes.php
     * @dataProvider getConfigurationTypes
     * @magentoAppIsolation enabled
     */
    public function testLoadConfigurations($type, $controlName, $controlData)
    {
        $designTheme = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_View_DesignInterface')
            ->getDesignTheme();
        $configuration = $this->_configFactory->create($type, $designTheme);
        $this->assertEquals($controlData, $configuration->getControlData($controlName));
    }

    /**
     * Data provider with sample data for test controls
     *
     * @return array
     */
    public function getConfigurationTypes()
    {
        return array(
            array(Magento_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_QUICK_STYLES, 'logo-uploader', array(
                'type'         => 'logo-uploader',
                'layoutParams' => array('title' => 'Logo Uploader', 'column' => 'center'),
                'attribute'    => 'background-image',
                'selector'     => '.test-logo-2',
                'var'          => 'Magento_DesignEditor::test_var_key4',
                'value'        => 'test_child_value4',
                'default'      => 'test_value4'
            )),
            array(Magento_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_QUICK_STYLES,
                'background-color-picker',
                array(
                    'type'         => 'color-picker',
                    'layoutParams' => array('title' => 'Background Color', 'column' => 'right'),
                    'selector'     => '.body .div',
                    'attribute'    => 'background-color',
                    'var'          => 'Magento_DesignEditor::test_var_key5',
                    'value'        => 'test_child_value5',
                    'default'      => 'test_value5'
                )
            ),
            array(Magento_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_IMAGE_SIZING, 'product-list', array(
                'type'         => 'image-sizing',
                'layoutParams' => array('title' => 'Up-sell Products List'),
                'components'   => array(
                    'image-type'   => array(
                        'type'    => 'image-type',
                        'var'     =>  'Magento_DesignEditor::test_var_key1',
                        'value'   => 'test_child_value1',
                        'default' => 'test_value1'
                    ),
                    'image-height' => array(
                        'type'    => 'image-height',
                        'var'     =>  'Magento_DesignEditor::test_var_key2',
                        'value'   => 'test_child_value2',
                        'default' => 'test_value2'
                    ),
                    'image-width'  => array(
                        'type'    => 'image-width',
                        'var'     =>  'Magento_DesignEditor::test_var_key3',
                        'value'   => 'test_child_value3',
                        'default' => 'test_value3'
                    ),
                )
            ))
        );
    }

    /**
     * Test control data
     *
     * @magentoDataFixture Magento/DesignEditor/Model/_files/design/themes.php
     * @dataProvider getSaveDataProvider
     * @magentoAppIsolation enabled
     */
    public function testSaveConfiguration($saveData, $xpathData)
    {
        $type = Magento_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_QUICK_STYLES;
        $theme = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_View_DesignInterface')
            ->getDesignTheme();
        $configuration = $this->_configFactory->create($type, $theme);
        $configuration->saveData($saveData);
        $this->assertFileExists($theme->getCustomization()->getCustomViewConfigPath());

        $actual = new DOMDocument();
        $actual->load($theme->getCustomization()->getCustomViewConfigPath());
        $domXpath = new DOMXPath($actual);
        foreach ($xpathData as $xpath => $isEmpty) {
            if ($isEmpty) {
                $this->assertEmpty($domXpath->query($xpath)->item(0));
            } else {
                $this->assertNotEmpty($domXpath->query($xpath)->item(0));
            }
        }
    }

    /**
     * Data provider for testing save functionality
     *
     * @return array
     */
    public function getSaveDataProvider()
    {
        return array(
            array(
                array(
                    'background-color-picker' => 'test_saved_value1',
                    'logo-uploader'           => 'test_saved_value2',
                    'image-uploader-empty'    => 'test_saved_value_empty',
                ),
                array(
                    '//var[text() = "test_saved_value1"]'      => false,
                    '//var[text() = "test_saved_value2"]'      => false,
                    '//var[text() = "test_saved_value_empty"]' => true,
                )
            )
        );
    }
}
