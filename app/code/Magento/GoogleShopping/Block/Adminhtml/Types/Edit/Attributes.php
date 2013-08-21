<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Attributes box for Google Content attributes mapping
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Block_Adminhtml_Types_Edit_Attributes
    extends Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{

    protected $_template = 'types/edit/attributes.phtml';

    /**
     * Preparing global layout
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Add New Attribute'),
            'class' => 'add',
            'id'    => 'add_new_attribute',
            'on_click' => 'gContentAttribute.add()'
        ));
        $this->addChild('delete_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Remove'),
            'class' => 'delete delete-product-option',
            'on_click' => 'gContentAttribute.remove(event)'
        ));

        return parent::_prepareLayout();
    }

    /**
     * Get attributes select field id
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'gcontent_attribute';
    }

    /**
     * Get attributes select field name
     *
     * @return string
     */
    public function getFieldName ()
    {
        return 'attributes';
    }

    /**
     * Build HTML code for select element which contains all available Google's attributes
     *
     * @return string
     */
    public function getGcontentAttributesSelectHtml()
    {
        $options[] = array('label' => __('Custom attribute, no mapping'));

        $attributesTree = Mage::getSingleton('Magento_GoogleShopping_Model_Config')
            ->getAttributesByCountry($this->getTargetCountry());

        foreach ($attributesTree as $destination => $attributes) {
            $options[] = array(
                'label' => $destination,
                'is_group' => true,
            );
            foreach ($attributes as $attribute => $params) {
                $options[$attribute] = array('label' => $params['name']);
                if ((int)$params['required'] == 1) {
                    $options[$attribute]['style'] = 'color: #940000;';
                }
            }
            $options[] = array(
                'is_group' => true,
                'is_close' => true
            );
        }

        $select = $this->getLayout()->createBlock('Magento_GoogleShopping_Block_Adminhtml_Types_Edit_Select')
            ->setId($this->getFieldId().'_{{index}}_gattribute')
            ->setName($this->getFieldName().'[{{index}}][gcontent_attribute]')
            ->setOptions($options);

        return $this->_toOneLineString($select->toHtml());
    }

    /**
     * Build HTML select element of attribute set attributes
     *
     * @param boolean $escapeJsQuotes
     * @return string
     */
    public function getAttributesSelectHtml($escapeJsQuotes = false)
    {
        $select = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Html_Select')
            ->setId($this->getFieldId().'_{{index}}_attribute')
            ->setName($this->getFieldName().'[{{index}}][attribute_id]')
            ->setOptions($this->_getAttributes($this->getAttributeSetId(), $escapeJsQuotes));
        return $select->getHtml();
    }

    /**
     * Get HTML code for button "Add New Attribute"
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Get HTML code for button "Remove"
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Get attributes of an attribute set
     * Skip attributes not needed for Google Content
     *
     * @param int $setId
     * @param boolean $escapeJsQuotes
     * @return array
     */
    public function _getAttributes($setId, $escapeJsQuotes = false)
    {
        $attributes = Mage::getModel('Magento_GoogleShopping_Model_Attribute')->getAllowedAttributes($setId);
        $result = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */
            $result[$attribute->getAttributeId()] = $escapeJsQuotes
                ? $this->jsQuoteEscape($attribute->getFrontendLabel())
                : $attribute->getFrontendLabel();
        }
        return $result;
    }

    /**
     * Encode the mixed $data into the JSON format
     *
     * @param mixed $data
     * @return string
     */
    public function jsonFormat($data)
    {
        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($data);
    }

    /**
     * Format string to one line, cut symbols \n and \r
     *
     * @param string $string
     * @return string
     */
    protected function _toOneLineString($string)
    {
        return str_replace(array("\r\n", "\n", "\r"), "", $string);
    }

}
