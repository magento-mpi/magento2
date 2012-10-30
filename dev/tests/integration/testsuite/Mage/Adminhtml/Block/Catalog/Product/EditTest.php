<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Edit
     */
    protected $_block = null;

    protected function setUp()
    {
        $this->_block = new Mage_Adminhtml_Block_Catalog_Product_Edit();
        Mage::register('current_product', new Varien_Object(array('type_id' => 'simple')));
    }

    public function testGetTypeSwitcherData()
    {
        $data = array(
            'current_type' => 'simple',
            'attributes' => array(),
        );
        $jsData = json_decode($this->_block->getTypeSwitcherData(), true);
        $this->assertEquals($data['current_type'], $jsData['current_type']);
        $this->assertEquals($data['attributes'], $jsData['attributes']);
    }
}
