<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Widget Form File Element Block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Form_Element_File extends Magento_Data_Form_Element_Abstract
{
    /**
     * @var Mage_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * Initialize Form Element
     *
     * @param Mage_Core_Model_View_Url $viewUrl
     * @param array $attributes
     */
    public  function __construct(Mage_Core_Model_View_Url $viewUrl, $attributes = array())
    {
        $this->_viewUrl = $viewUrl;
        parent::__construct($attributes);
        $this->setType('file');
    }

    /**
     * Return Form Element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('input-file');
        if ($this->getRequired()) {
            $this->removeClass('required-entry');
            $this->addClass('required-file');
        }

        $element = sprintf('<input id="%s" name="%s" %s />%s%s',
            $this->getHtmlId(),
            $this->getName(),
            $this->serialize($this->getHtmlAttributes()),
            $this->getAfterElementHtml(),
            $this->_getHiddenInput()
        );

        return $this->_getPreviewHtml() . $element . $this->_getDeleteCheckboxHtml();
    }

    /**
     * Return Delete File CheckBox HTML
     *
     * @return string
     */
    protected function _getDeleteCheckboxHtml()
    {
        $html = '';
        if ($this->getValue() && !$this->getRequired() && !is_array($this->getValue())) {
            $checkboxId = sprintf('%s_delete', $this->getHtmlId());
            $checkbox   = array(
                'type'  => 'checkbox',
                'name'  => sprintf('%s[delete]', $this->getName()),
                'value' => '1',
                'class' => 'checkbox',
                'id'    => $checkboxId
            );
            $label      = array(
                'for'   => $checkboxId
            );
            if ($this->getDisabled()) {
                $checkbox['disabled'] = 'disabled';
                $label['class'] = 'disabled';
            }

            $html .= '<span class="' . $this->_getDeleteCheckboxSpanClass() . '">';
            $html .= $this->_drawElementHtml('input', $checkbox) . ' ';
            $html .= $this->_drawElementHtml('label', $label, false) . $this->_getDeleteCheckboxLabel() . '</label>';
            $html .= '</span>';
        }
        return $html;
    }

    /**
     * Return Delete CheckBox SPAN Class name
     *
     * @return string
     */
    protected function _getDeleteCheckboxSpanClass()
    {
        return 'delete-file';
    }

    /**
     * Return Delete CheckBox Label
     *
     * @return string
     */
    protected function _getDeleteCheckboxLabel()
    {
        return Mage::helper('Mage_Adminhtml_Helper_Data')->__('Delete File');
    }

    /**
     * Return File preview link HTML
     *
     * @return string
     */
    protected function _getPreviewHtml()
    {
        $html = '';
        if ($this->getValue() && !is_array($this->getValue())) {
            $image = array(
                'alt'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Download'),
                'title' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Download'),
                'src'   => $this->_viewUrl->getViewFileUrl('images/fam_bullet_disk.gif'),
                'class' => 'v-middle'
            );
            $url = $this->_getPreviewUrl();
            $html .= '<span>';
            $html .= '<a href="' . $url . '">' . $this->_drawElementHtml('img', $image) . '</a> ';
            $html .= '<a href="' . $url . '">' . Mage::helper('Mage_Adminhtml_Helper_Data')->__('Download') . '</a>';
            $html .= '</span>';
        }
        return $html;
    }

    /**
     * Return Hidden element with current value
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return $this->_drawElementHtml('input', array(
            'type'  => 'hidden',
            'name'  => sprintf('%s[value]', $this->getName()),
            'id'    => sprintf('%s_value', $this->getHtmlId()),
            'value' => $this->getEscapedValue()
        ));
    }

    /**
     * Return Preview/Download URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        return Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl('adminhtml/customer/viewfile', array(
            'file' => Mage::helper('Mage_Core_Helper_Data')->urlEncode($this->getValue()),
        ));
    }

    /**
     * Return Element HTML
     *
     * @param string $element
     * @param array $attributes
     * @param boolean $closed
     * @return string
     */
    protected function _drawElementHtml($element, array $attributes, $closed = true)
    {
        $parts = array();
        foreach ($attributes as $k => $v) {
            $parts[] = sprintf('%s="%s"', $k, $v);
        }

        return sprintf('<%s %s%s>', $element, implode(' ', $parts), $closed ? ' /' : '');
    }

    /**
     * Return escaped value
     *
     * @param int $index
     * @return string
     */
    public function getEscapedValue($index = null)
    {
        if (is_array($this->getValue())) {
            return false;
        }
        $value = $this->getValue();
        if (is_array($value) && is_null($index)) {
            $index = 'value';
        }

        return parent::getEscapedValue($index);
    }
}
