<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Widget
 */
class Mage_Widget_Model_Widget_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Model_Widget_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Widget_Model_Widget_Config;
    }

    /**
     * App isolation is enabled, because we change current area and design
     *
     * @magentoAppIsolation enabled
     */
    public function testGetPluginSettings()
    {
        Mage::getDesign()->setArea('adminhtml')
            ->setPackageName('default')
            ->setTheme('default')
            ->setSkin('default');

        $config = new Varien_Object();
        $settings = $this->_model->getPluginSettings($config);

        $this->assertArrayHasKey('widget_plugin_src', $settings);
        $this->assertArrayHasKey('widget_placeholders', $settings);
        $this->assertArrayHasKey('widget_window_url', $settings);

        $js = $settings['widget_plugin_src'];
        $this->assertStringStartsWith('http://localhost/js/', $js);
        $this->assertStringEndsWith('editor_plugin.js', $js);

        $this->assertInternalType('array', $settings['widget_placeholders']);

        $this->assertStringStartsWith('http://localhost/', $settings['widget_window_url']);
    }
}
