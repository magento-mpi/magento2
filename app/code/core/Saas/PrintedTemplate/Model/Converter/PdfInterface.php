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
 * The interface for generating PDF code from HTML
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
interface Saas_PrintedTemplate_Model_Converter_PdfInterface
{
    /**
     * Get PDF code
     *
     * @return string PDF code
     */
    public function getPdf();
}
