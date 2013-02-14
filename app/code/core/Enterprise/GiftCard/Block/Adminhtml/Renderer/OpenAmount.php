<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * HTML select element block
 *
 * @category   Enterprise
 * @package    Enterprise_GiftCard
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCard_Block_Adminhtml_Renderer_OpenAmount extends Varien_Data_Form_Element_Select
{
    /**
     * @var Varien_Data_Form_Element_Checkbox
     */
    protected $_element;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_element = Mage::getModel('Varien_Data_Form_Element_Checkbox');
        parent::__construct($data);
    }

    /**
     * Set form to element
     *
     * @param $form
     * @return Varien_Data_Form
     */
    public function setForm($form)
    {
        $this->_element->setForm($form);
        return parent::setForm($form);
    }

    /**
     * Return rendered field
     *
     * @return string
     */
    public function getElementHtml()
    {
        $this->_element->setId($this->getHtmlId())->setName($this->getData('name'))->setChecked(true);
        return $this->_element->getElementHtml() . ' ' . Mage::helper('Enterprise_GiftCard_Helper_Data')->__('allow');
    }
}
