<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Variable;

/**
 * @magentoAppArea adminhtml
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Variable\Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Variable\Config');
    }

    public function testGetWysiwygJsPluginSrc()
    {
        $src = $this->_model->getWysiwygJsPluginSrc();
        $this->assertStringStartsWith('http://localhost/pub/static/adminhtml/magento_backend/en_US/mage/adminhtml/',
            $src);
        $this->assertStringEndsWith('editor_plugin.js', $src);
    }
}
