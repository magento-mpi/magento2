<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_WeightTest extends PHPUnit_Framework_TestCase
{
    const VIRTUAL_FIELD_HTML_ID = 'weight_and_type_switcher';

    /**
     * @var Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight
     */
    protected $_model;

    /**
     * @var Magento_Data_Form_Element_Checkbox
     */
    protected $_virtual;

    public function testSetForm()
    {
        $coreHelper = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $factory = $this->getMock('Magento_Data_Form_Element_Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock('Magento_Data_Form_Element_CollectionFactory', array('create'),
            array(), '', false);

        $form = new Magento_Data_Form($factory, $collectionFactory);

        $helper = $this->getMock('Magento_Catalog_Helper_Product', array('getTypeSwitcherControlLabel'),
            array(), '', false, false
        );
        $helper->expects($this->any())->method('getTypeSwitcherControlLabel')
            ->will($this->returnValue('Virtual / Downloadable'));

        $this->_virtual = $this->getMock('Magento_Data_Form_Element_Checkbox',
            array('setId', 'setName', 'setLabel', 'setForm'),
            array(), '', false, false);
        $this->_virtual->expects($this->any())
            ->method('setId')
            ->will($this->returnSelf());
        $this->_virtual->expects($this->any())
            ->method('setName')
            ->will($this->returnSelf());
        $this->_virtual->expects($this->any())
            ->method('setLabel')
            ->will($this->returnSelf());
        $this->_virtual->expects($this->any())
            ->method('setForm')
            ->with($this->equalTo($form))
            ->will($this->returnSelf());

        $factory->expects($this->once())
            ->method('create')
            ->with($this->equalTo('checkbox'))
            ->will($this->returnValue($this->_virtual));

        $this->_model = new Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Weight($coreHelper, $factory,
            $collectionFactory, $helper);
        $this->_model->setForm($form);
    }
}
