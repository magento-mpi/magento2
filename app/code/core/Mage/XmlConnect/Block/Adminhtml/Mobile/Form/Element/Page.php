<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect page form element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Page
    extends Varien_Data_Form_Element_Abstract
{
    /**
     * Init page element
     *
     * @param array $attributes
     */
    protected function _construct($attributes=array())
    {
        parent::_construct($attributes);
        $this->setType('page');
    }

    /**
     * Setting stored data to page element
     *
     * @param array $conf
     */
    public function initFields($conf)
    {
        $this->addElement(new Varien_Data_Form_Element_Text(array(
            'name'  => $conf['name'] . '[label]',
            'class' => 'label onclick_text',
        )));

        $this->addElement(new Varien_Data_Form_Element_Select(array(
            'name'      => $conf['name'] . '[id]',
            'values'    => $conf['values'],
        )));
    }

    /**
     * Add form element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param boolean|string $after also can be '^'
     * @return Varien_Data_Form
     */
    public function addElement(Varien_Data_Form_Element_Abstract $element, $after = false)
    {
        $element->setId($element->getData('name'));
        parent::addElement($element, $after);
    }

    /**
     * Getter for Label field
     * fetching first element as label
     *
     * @param string $idSuffix
     * @return string
     */
    public function getLabelHtml($idSuffix = '')
    {
        list($label, $element) = $this->getElements();
        return $label->toHtml();
    }

    /**
     * Getter for second part of rendered field ("selectbox" and "delete button")
     * fetching second element as <element code>
     *
     * @return string
     */
    public function getElementHtml()
    {
        list($label, $element) = $this->getElements();
        return $element->toHtml()
            . '</td><td class="label" style="width: 5em">'
            . '<button class="scalable save onclick_button" value="&minus;"><span><span><span>'
            . Mage::helper('Mage_XmlConnect_Helper_Data')->__('Delete')
            . '</span></span></span></button>';
    }
}
