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
 * Interface for generate PDF code from HTML
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
interface Saas_PrintedTemplate_Model_Converter_PdfAdapter_Interface
{
    /**
     * Value for command line page orientation portrait.
     * This value is also used as default if parameter isn't specified
     */
    const PAGE_ORIENTATION_PORTRAIT = 'portrait';

    /**
     * Value for command line for page orientation lansdcape
     */
    const PAGE_ORIENTATION_LANDSCAPE = 'landscape';

    /**
     * Set PDF header settings
     *
     * @param string $html
     * @param Zend_Measure_Length $height
     * @return Saas_PrintedTemplate_Model_Converter_PdfAdapter_Interface
     */
    public function setupHeader($html, Zend_Measure_Length $height = null);

    /**
     * Set PDF footer settings
     *
     * @param string $html
     * @param Zend_Measure_Length $height
     * @return Saas_PrintedTemplate_Model_Converter_PdfAdapter_Interface
     */
    public function setupFooter($html, Zend_Measure_Length $height = null);

    /**
     * Rendering PDF from HTML
     * Return PDF code for specified HTML code
     *
     * @param string $bodyHtml
     * @param Saas_PrintedTemplate_Model_PageSize $size
     * @param int|null $orientation PAGE_ORIENTATION_PORTRAIT|PAGE_ORIENTATION_LANDSCAPE|null
     * @return string
     * @throws Magento_Core_Exception
     */
    public function renderHtml(
        $bodyHtml,
        Saas_PrintedTemplate_Model_PageSize $pageSize = null,
        $pageOrientation = null
    );

    /**
     * Can Header and Footer heights be calculated dynamically if their sizes aren't specified
     *
     * @return bool
     */
    public function canCalculateHeightsDynamically();
}
