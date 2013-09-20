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

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Variable\Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Mage::getModel('Magento\Core\Model\Variable\Config');
    }

    public function testGetWysiwygJsPluginSrc()
    {
        $src = $this->_model->getWysiwygJsPluginSrc();
        $this->assertStringStartsWith('http://localhost/pub/lib/', $src);
        $this->assertStringEndsWith('editor_plugin.js', $src);
    }
}
