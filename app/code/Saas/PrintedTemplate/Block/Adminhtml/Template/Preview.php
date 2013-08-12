<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Template model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Adminhtml_Template_Preview extends Magento_Backend_Block_Widget
{
    /**
     * Container for HTML preview
     *
     * @var Saas_PrintedTemplate_Model_Converter_HtmlInterface
     */
    protected $_previewContainer = null;

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        // Calculate page width for CSS
        $tpl = $this->_getTemplate();
        $width = $tpl->getPageOrientation() !=
            Saas_PrintedTemplate_Model_Converter_PdfAdapter_Interface::PAGE_ORIENTATION_LANDSCAPE
            ? $tpl->getPageSize()->getWidth()
            : $tpl->getPageSize()->getHeight();

        $this->setWidth($width->setType(Zend_Measure_Length::MILLIMETER)->getValue() . 'mm');

        return parent::_beforeToHtml();
    }

    /**
     * Get preview convertor to retrieve HTML from it
     *
     * @return Saas_PrintedTemplate_Model_Converter_HtmlInterface
     */
    public function getPreviewConverter()
    {
        if (is_null($this->_previewContainer)) {
            $model = Mage::getModel(
                'Saas_PrintedTemplate_Model_Converter_Preview_Mock_' . ucfirst($this->_getTemplate()->getEntityType()))
                ->setOrder(Mage::getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order'));

            $this->_previewContainer = Mage::helper('Saas_PrintedTemplate_Helper_Locator')
                ->getConverter($model, $this->_getTemplate());
        }

        return $this->_previewContainer;
    }

    /**
     * Returns printed template model
     *
     * @return Saas_PrintedTemplate_Model_Template
     */
    protected function _getTemplate()
    {
        return Mage::registry('printed_template');
    }

    /**
     * Delete all scripts from output
     *
     * @param string $string
     * @return string
     */
    protected function _filterScriptTags($string)
    {
        return preg_replace('!<script[^>]*>.*</script>!isU', '', $string);
    }
}
