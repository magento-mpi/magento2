<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Data_Form
     */
    protected $_form = null;

    /**
     * Initialize block
     */
    protected function setUp()
    {
        parent::setUp();
        Mage::register('current_urlrewrite', new Varien_Object(array('id' => 3)));
    }

    /**
     * Initialize form
     */
    protected function _initForm()
    {
        $layout = new Mage_Core_Model_Layout();
        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form', 'block');
        $block->toHtml();
        $this->_form = $block->getForm();
    }

    /**
     * Unset block
     */
    protected function tearDown()
    {
        Mage::unregister('current_urlrewrite');
        Mage::unregister('current_product');
        Mage::unregister('current_category');
        unset($this->_form);
        parent::tearDown();
    }

    public function testProductUrlrewrite()
    {
        Mage::register('current_product', new Varien_Object(array('id' => 2)));
        $this->_initForm();
        $this->assertContains('product/2', $this->_form->getAction());
    }

    public function testProductWithCategoryUrlrewrite()
    {
        Mage::register('current_product', new Varien_Object(array('id' => 2)));
        Mage::register('current_category', new Varien_Object(array('id' => 5)));
        $this->_initForm();
        $this->assertContains('product/2/category/5', $this->_form->getAction());

    }

    public function testCategoryUrlrewrite()
    {
        Mage::register('current_category', new Varien_Object(array('id' => 5)));
        $this->_initForm();
        $this->assertContains('category/5', $this->_form->getAction());
    }
}
