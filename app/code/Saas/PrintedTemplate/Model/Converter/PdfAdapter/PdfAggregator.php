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
 * PDF documents aggregator
 * Merges together several documents into one
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_PdfAdapter_PdfAggregator
    implements Saas_PrintedTemplate_Model_Converter_PdfAdapter_PdfAggregatorInterface
{
    /**
     * Merge several pdf documents into one
     * @uses Zend_Pdf  need Zend_Pdf version 1.11 or higher
     *
     * @param array $pdfDocuments
     * @return string
     * @throws Exception On incorrect argument
     */
    public function render(array $pdfDocuments)
    {
        $resultPdf = new Zend_Pdf();
        $pdfExtractor = new Zend_Pdf_Resource_Extractor();

        foreach ($pdfDocuments as $pdf) {
            $pdfItem = new Zend_Pdf($pdf);
            foreach ($pdfItem->pages as $page) {
                $resultPdf->pages[] = $pdfExtractor->clonePage($page);
            }
        }

        return $resultPdf->render();
    }
}
