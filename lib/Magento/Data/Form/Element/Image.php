<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Category form input image element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Element_Image extends Magento_Data_Form_Element_Abstract
{
    /**
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlBuilder;
    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param array $attributes
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        Magento_Core_Model_UrlInterface $urlBuilder,
        $attributes = array()
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);
        $this->setType('file');
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';

        if ((string)$this->getValue()) {
            $url = $this->_getUrl();

            if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
                $url = $this->_urlBuilder->getBaseUrl('media') . $url;
            }

            $html = '<a href="' . $url . '"'
                . ' onclick="imagePreview(\'' . $this->getHtmlId() . '_image\'); return false;" ' . $this->_getUiId('link') . '>'
                . '<img src="' . $url . '" id="' . $this->getHtmlId() . '_image" title="' . $this->getValue() . '"'
                . ' alt="' . $this->getValue() . '" height="22" width="22" class="small-image-preview v-middle"  ' . $this->_getUiId() . ' />'
                . '</a> ';
        }
        $this->setClass('input-file');
        $html .= parent::getElementHtml();
        $html .= $this->_getDeleteCheckbox();

        return $html;
    }

    /**
     * Return html code of delete checkbox element
     *
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $label = __('Delete Image');
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox"'
                . ' name="' . parent::getName() . '[delete]" value="1" class="checkbox"'
                . ' id="' . $this->getHtmlId() . '_delete"' . ($this->getDisabled() ? ' disabled="disabled"': '')
                . '/>';
            $html .= '<label for="' . $this->getHtmlId() . '_delete"'
                . ($this->getDisabled() ? ' class="disabled"' : '') . '> ' . $label . '</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }

    /**
     * Return html code of hidden element
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="' . parent::getName() . '[value]" value="' . $this->getValue() . '" />';
    }

    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return  $this->getData('name');
    }
}
