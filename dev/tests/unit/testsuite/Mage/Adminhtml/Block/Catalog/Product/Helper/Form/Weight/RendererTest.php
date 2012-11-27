<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight_RendererTest extends PHPUnit_Framework_TestCase
{
    const VIRTUAL_FIELD_HTML_ID = 'weight_and_type_switcher';

    /**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Stock
     */
    protected $_model;

    /**
     * @var Varien_Data_Form_Element_Checkbox
     */
    protected $_virtual;

    public function testSetForm()
    {
        $this->_virtual = $this->getMock('Varien_Data_Form_Element_Checkbox',
            array('setForm', 'setId', 'setName', 'setLabel')
        );
        $this->_virtual->expects($this->any())->method('setId')->with($this->equalTo(self::VIRTUAL_FIELD_HTML_ID))
            ->will($this->returnValue($this->_virtual));
        $this->_virtual->expects($this->any())->method('setName')->with($this->equalTo('is_virtual'))
            ->will($this->returnValue($this->_virtual));;
        $this->_virtual->expects($this->any())->method('setLabel')->with($this->equalTo('Virtual / Downloadable'));
        $helper = $this->getMock('Mage_Catalog_Helper_Data', array('getIsVirtualControlLabel'));
        $helper->expects($this->any())->method('getIsVirtualControlLabel')
            ->will($this->returnValue('Virtual / Downloadable'));
        $this->_model = new Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight_Renderer(
            array('element' => $this->_virtual, 'helper' => $helper)
        );
        $form = new Varien_Data_Form();
        $this->_virtual->expects($this->any())->method('setForm')->with($this->isInstanceOf('Varien_Data_Form'));
        $this->_model->setForm($form);
    }
}
