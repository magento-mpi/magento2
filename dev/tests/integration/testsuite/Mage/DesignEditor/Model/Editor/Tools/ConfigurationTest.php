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

class Mage_DesignEditor_Model_Editor_Tools_ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Model_Config_Control_Factory
     */
    protected $_configFactory;

    /**
     * Initialize dependencies
     */
    protected function setUp()
    {
        $design = Mage::getObjectManager()->get('Mage_Core_Model_Design_Package');
        $design->setDesignTheme('package/test_child', Mage_Core_Model_Design_Package::DEFAULT_AREA);
        $this->_configFactory = Mage::getObjectManager()->create('Mage_DesignEditor_Model_Config_Control_Factory');
    }

    /**
     * Test control data
     *
     * @magentoDataFixture Mage/DesignEditor/Model/_files/design/themes.php
     * @dataProvider getConfigurationTypes
     * @magentoAppIsolation enabled
     */
    public function testLoadConfigurations($type, $controlName, $controlData)
    {
        $quickStylesConf = $this->_configFactory->create($type);
        /** @var $configuration Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration */
        $configuration = Mage::getObjectManager()->create('Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration',
            array($quickStylesConf, Mage::getDesign()->getDesignTheme()));

        $this->assertNotEmpty($configuration);
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
            array(Mage_DesignEditor_Model_Config_Control_Factory::TYPE_QUICK_STYLES, 'logo-uploader', array(
                'type'         => 'logo-uploader',
                'layoutParams' => array('title' => 'Logo Uploader', 'column' => 'center'),
                'var'          => 'test_var_key4',
                'value'        => 'test_child_value4',
                'default'      => 'test_value4'
            )),
            array(Mage_DesignEditor_Model_Config_Control_Factory::TYPE_QUICK_STYLES, 'background-color-picker', array(
                'type'         => 'color-picker',
                'layoutParams' => array('title' => 'Background Color', 'column' => 'right'),
                'selector'     => '.body .div',
                'attribute'    => 'background-color',
                'var'          => 'test_var_key5',
                'value'        => 'test_child_value5',
                'default'      => 'test_value5'
            ))
        );
    }


}
