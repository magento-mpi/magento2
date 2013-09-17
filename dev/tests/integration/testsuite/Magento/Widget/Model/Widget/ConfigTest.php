<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Widget_Model_Widget_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Widget_Model_Widget_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Widget_Model_Widget_Config');
    }

    /**
     * App isolation is enabled, because we change current area and design
     *
     * @magentoAppIsolation enabled
     */
    public function testGetPluginSettings()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setDesignTheme('magento_basic', 'adminhtml');

        $config = new Magento_Object();
        $settings = $this->_model->getPluginSettings($config);

        $this->assertArrayHasKey('widget_plugin_src', $settings);
        $this->assertArrayHasKey('widget_placeholders', $settings);
        $this->assertArrayHasKey('widget_window_url', $settings);

        $jsFilename = $settings['widget_plugin_src'];
        $this->assertStringStartsWith('http://localhost/pub/lib/', $jsFilename);
        $this->assertStringEndsWith('editor_plugin.js', $jsFilename);

        $this->assertInternalType('array', $settings['widget_placeholders']);

        $this->assertStringStartsWith('http://localhost/index.php/key', $settings['widget_window_url']);
    }

    public function testGetWidgetWindowUrl()
    {
        $config = new Magento_Object(array('widget_filters' =>  array('is_email_compatible' => 1)));

        $url = $this->_model->getWidgetWindowUrl($config);

        $this->assertStringStartsWith('http://localhost/index.php/skip_widgets/', $url);
    }
}
