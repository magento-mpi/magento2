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
 * The interface for generating HTML code of document
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
interface Saas_PrintedTemplate_Model_Converter_HtmlInterface
{
    /**
     * Get page contenet HTML code
     *
     * @return string HTML code
     */
    public function getContentHtml();

    /**
     * Get page header HTML code
     *
     * @return string HTML code
     */
    public function getHeaderHtml();

    /**
     * Get page footer HTML code
     *
     * @return string HTML code
     */
    public function getFooterHtml();
}
