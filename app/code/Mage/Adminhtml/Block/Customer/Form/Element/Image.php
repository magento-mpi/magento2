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
 * Customer Widget Form Image File Element Block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Form_Element_Image extends Mage_Adminhtml_Block_Customer_Form_Element_File
{
    /**
     * Return Delete CheckBox Label
     *
     * @return string
     */
    protected function _getDeleteCheckboxLabel()
    {
        return __('Delete Image');
    }

    /**
     * Return Delete CheckBox SPAN Class name
     *
     * @return string
     */
    protected function _getDeleteCheckboxSpanClass()
    {
        return 'delete-image';
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
            $url = $this->_getPreviewUrl();
            $imageId = sprintf('%s_image', $this->getHtmlId());
            $image   = array(
                'alt'    => __('View Full Size'),
                'title'  => __('View Full Size'),
                'src'    => $url,
                'class'  => 'small-image-preview v-middle',
                'height' => 22,
                'width'  => 22,
                'id'     => $imageId
            );
            $link    = array(
                'href'      => $url,
                'onclick'   => "imagePreview('{$imageId}'); return false;",
            );

            $html = sprintf('%s%s</a> ',
                $this->_drawElementHtml('a', $link, false),
                $this->_drawElementHtml('img', $image)
            );
        }
        return $html;
    }

    /**
     * Return Image URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        if (is_array($this->getValue())) {
            return false;
        }
        return Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl('adminhtml/customer/viewfile', array(
            'image'      => Mage::helper('Mage_Core_Helper_Data')->urlEncode($this->getValue()),
        ));
    }
}
