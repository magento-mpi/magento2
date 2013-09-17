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

class Magento_Core_Model_Layout_ElementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Element
     */
    protected $_model;

    public function testPrepare()
    {
        /**
         * @TODO: Need to use ObjectManager instead 'new'.
         * On this moment we have next bug MAGETWO-4274 which blocker for this key.
         */
        $this->_model = new Magento_Core_Model_Layout_Element(__DIR__ . '/../_files/_layout_update.xml', 0, true);

        list($blockNode) = $this->_model->xpath('//block[@name="head"]');
        list($actionNode) = $this->_model->xpath('//action[@method="setTitle"]');

        $this->assertEmpty($blockNode->attributes()->parent);
        $this->assertEmpty($actionNode->attributes()->block);

        $this->_model->prepare(array());

        $this->assertEquals('root', (string)$blockNode->attributes()->parent);
        $this->assertEquals('Magento_Adminhtml_Block_Page_Head', (string)$blockNode->attributes()->class);
        $this->assertEquals('head', (string)$actionNode->attributes()->block);
    }
}
