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
namespace Magento\Widget\Model\Widget;

/**
 * @magentoAppArea adminhtml
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Model\Widget\Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Widget\Model\Widget\Config'
        );
    }

    /**
     * App isolation is enabled, because we change current area and design
     *
     * @magentoAppIsolation enabled
     */
    public function testGetPluginSettings()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\DesignInterface'
        )->setDesignTheme(
            'Magento/backend'
        );

        $config = new \Magento\Object();
        $settings = $this->_model->getPluginSettings($config);

        $this->assertArrayHasKey('widget_plugin_src', $settings);
        $this->assertArrayHasKey('widget_placeholders', $settings);
        $this->assertArrayHasKey('widget_window_url', $settings);

        $jsFilename = $settings['widget_plugin_src'];
        $this->assertStringStartsWith('http://localhost/pub/lib/', $jsFilename);
        $this->assertStringEndsWith('editor_plugin.js', $jsFilename);

        $this->assertInternalType('array', $settings['widget_placeholders']);

        $this->assertStringStartsWith(
            'http://localhost/index.php/backend/admin/widget/index/key',
            $settings['widget_window_url']
        );
    }

    public function testGetWidgetWindowUrl()
    {
        $config = new \Magento\Object(array('widget_filters' => array('is_email_compatible' => 1)));

        $url = $this->_model->getWidgetWindowUrl($config);

        $this->assertStringStartsWith('http://localhost/index.php/backend/admin/widget/index/skip_widgets', $url);
    }
}
