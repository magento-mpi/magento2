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

class Magento_Core_Model_Variable_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Variable_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Variable_Config');
    }

    public function testGetWysiwygJsPluginSrc()
    {
        $src = $this->_model->getWysiwygJsPluginSrc();
        $this->assertStringStartsWith('http://localhost/pub/lib/', $src);
        $this->assertStringEndsWith('editor_plugin.js', $src);
    }
}
