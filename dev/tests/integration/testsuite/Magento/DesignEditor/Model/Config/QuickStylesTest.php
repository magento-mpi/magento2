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

class Magento_DesignEditor_Model_Config_QuickStylesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_DesignEditor_Model_Config_Control_QuickStyles
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_design;

    /**
     * @var Magento_Core_Model_View_FileSystem
     */
    protected $_viewFileSystem;

    /**
     * Initialize dependencies
     */
    protected function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_design = $objectManager->get('Magento_Core_Model_View_DesignInterface');
        $this->_design->setDesignTheme('vendor_test', Magento_Core_Model_View_DesignInterface::DEFAULT_AREA);
        $this->_viewFileSystem = $objectManager->get('Magento_Core_Model_View_FileSystem');
        $quickStylesPath = $this->_viewFileSystem->getFilename('Magento_DesignEditor::controls/quick_styles.xml');
        $this->assertFileExists($quickStylesPath);
        $this->_model = $objectManager->create(
            'Magento_DesignEditor_Model_Config_Control_QuickStyles',
            array('configFiles' => array($quickStylesPath))
        );
    }

    /**
     * Test control data
     *
     * @magentoDataFixture Magento/DesignEditor/Model/_files/design/themes.php
     * @dataProvider getTestDataProvider
     * @magentoAppIsolation enabled
     * @param string $controlName
     * @param array $expectedControlData
     */
    public function testLoadConfiguration($controlName, $expectedControlData)
    {
        $this->assertEquals($expectedControlData, $this->_model->getControlData($controlName));
    }

    /**
     * Data provider with sample data for test controls
     *
     * @return array
     */
    public function getTestDataProvider()
    {
        return array(
            array('headers', array(
                'type'         => 'logo',
                'layoutParams' => array('title' => 'Headers', 'column' => 'left'),
                'components'   => array (
                    'logo-picker'   => array (
                        'type'      => 'color-picker',
                        'selector'  => '.body .div',
                        'attribute' => 'background-color',
                        'var'       => 'Magento_DesignEditor::test_var_key1',
                    ),
                    'font-selector' => array (
                        'type'      => 'font-selector',
                        'selector'  => '*',
                        'attribute' => 'font-family',
                        'options'   => array('Arial, Verdana, Georgia', 'Tahoma'),
                        'var'       => 'Magento_DesignEditor::test_var_key2',
                    ),
                    'test-control' => array (
                        'type'       => 'test-control',
                        'components' => array (
                            'image-uploader' => array (
                                'type'      => 'logo-uploader',
                                'selector'  => '.test-logo-1',
                                'attribute' => 'background-image',
                                'var'       => 'Magento_DesignEditor::test_var_key3',
                            )
                        )
                    )
                )
            )),
            array('logo-uploader', array(
                'type'         => 'logo-uploader',
                'selector'     => '.test-logo-2',
                'attribute'    => 'background-image',
                'layoutParams' => array('title' => 'Logo Uploader', 'column' => 'center'),
                'var'          => 'Magento_DesignEditor::test_var_key4',
            )),
            array('background-color-picker', array(
                'type'         => 'color-picker',
                'layoutParams' => array('title' => 'Background Color', 'column' => 'right'),
                'selector'     => '.body .div',
                'attribute'    => 'background-color',
                'var'          => 'Magento_DesignEditor::test_var_key5',
            )),
        );
    }
}
