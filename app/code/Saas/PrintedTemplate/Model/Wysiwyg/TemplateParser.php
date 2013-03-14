<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Paser for parsint template footer and header from content using separators.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser
{
    /**
     * Parse content string which containg header/footer separators and set this data to template model
     *
     * @param string $content
     * @param Saas_PrintedTemplate_Model_Template $template
     * @return Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser self
     */
    public function importContent($content, Saas_PrintedTemplate_Model_Template $template)
    {
        $footerSeparator = $this->_getWysiwygConfig()->getFooterSeparator();
        $footer = '';
        if (false !== strpos($content, $footerSeparator)) {
            list ($content, $footer) = explode($footerSeparator, $content, 2);
        }
        $template->setFooter($footer);

        $headerSeparator = $this->_getWysiwygConfig()->getHeaderSeparator();
        $header = '';
        if (false !== strpos($content, $headerSeparator)) {
            list ($header, $content) = explode($headerSeparator, $content, 2);
        }
        $template->setHeader($header);

        $template->setContent($content);

        return $this;
    }

    /**
     * Assamble template's header, footer and content in one string using separators.
     *
     * @param Saas_PrintedTemplate_Model_Template $template
     * @return string
     */
    public function exportContent(Saas_PrintedTemplate_Model_Template $template)
    {
        $fullContent = $template->getContent();

        $headerSeparator = $this->_getWysiwygConfig()->getHeaderSeparator();
        if ($template->hasHeader() && $template->getHeader() != '') {
            $fullContent = $template->getHeader() . $headerSeparator . $fullContent;
        }

        $footerSeparator = $this->_getWysiwygConfig()->getFooterSeparator();
        if ($template->hasFooter() && $template->getFooter() != '') {
            $fullContent .= $footerSeparator . $template->getFooter();
        }

        return $fullContent;
    }

    /**
     * Get WYSIWYG config model instance
     *
     * @return Saas_PrintedTemplate_Model_Wysiwyg_Config
     */
    protected function _getWysiwygConfig()
    {
        return Mage::getSingleton('Saas_PrintedTemplate_Model_Wysiwyg_Config');
    }
}
