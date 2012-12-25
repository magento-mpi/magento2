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
 * Interface for PDF aggregator
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
interface Saas_PrintedTemplate_Model_Converter_PdfAdapter_PdfAggregatorInterface
{
    /**
     * Merge several PDF documents into one
     * Return merged PDFs code
     *
     * @param array $pdfDocuments List of PDF documents
     * @return string Result Pdf document
     */
    public function render(array $pdfDocuments);
}
