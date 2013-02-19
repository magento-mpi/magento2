<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Config_QuickStylesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Model_Config_QuickStyles
     */
    protected $_model;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * Initialize dependencies
     */
    protected function setUp()
    {
        $this->_design = Mage::getObjectManager()->get('Mage_Core_Model_Design_Package');
        $this->_design->setDesignTheme('package/test', Mage_Core_Model_App_Area::AREA_ADMINHTML);
        $quickStylesPath = $this->_design->getFilename('quick_styles/quick_styles.xml');
        $this->assertFileExists($quickStylesPath);
        $this->_model = Mage::getObjectManager()->create('Mage_DesignEditor_Model_Config_QuickStyles',
            array(array($quickStylesPath)));
    }

    /**
     * Test control data
     *
     * @magentoDataFixture Mage/DesignEditor/Model/_files/design/themes.php
     * @dataProvider getTestDataProvider
     * @magentoAppIsolation enabled
     */
    public function testLoadConfiguration($controlName, $controlData)
    {
        $this->assertEquals($this->_model->getGroupData($controlName), $controlData);
    }

    /**
     * Data provider with sample data for
     * @return array
     */
    public function getTestDataProvider()
    {
        return array(
            array('headers', array(
                'type'       => 'logo',
                'components' => array (
                    'logo-picker'   => array (
                        'type'      => 'color-picker',
                        'selector'  => '.body .div',
                        'attribute' => 'background-color',
                        'var'       => 'test_var_key1',
                    ),
                    'font-selector' => array (
                        'type'      => 'font-selector',
                        'selector'  => '*',
                        'attribute' => 'font-family',
                        'options'   => array('Arial, Verdana, Georgia', 'Tahoma'),
                        'var'       => 'test_var_key2',
                    ),
                    'test-control'  => array (
                        'type'       => 'test-control',
                        'components' => array (
                            'image-uploader' => array (
                                'type'    => 'logo-uploader',
                                'var'     => 'test_var_key3',
                            )
                        )
                    )
                )
            )),
            array('logo-uploader', array(
                'type'    => 'logo-uploader',
                'var'     => 'test_var_key4',
            )),
            array('background-color-picker', array(
                'type'      => 'color-picker',
                'selector'  => '.body .div',
                'attribute' => 'background-color',
                'var'       => 'test_var_key5',
            )),
        );
    }
}
