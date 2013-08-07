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
 * Converter for rendering a batch of models
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Batch implements Saas_PrintedTemplate_Model_Converter_PdfInterface
{
    /**
     * Collection (array) of objects which should be printed together
     *
     * @var array|Magento_Data_Collection
     */
    protected $_collection;

    /**
     * Construct batch converter
     *
     * @param array|Magento_Data_Collection $collection
     */
    public function __construct($collection)
    {
        $this->_collection = $collection;
    }

    /**
     * Render batch of models using specified converter
     *
     * @return string
     */
    public function getPdf()
    {
        $pdfDocs = array();
        foreach ($this->_collection as $model) {
            $pdfDocs[] = Mage::helper('Saas_PrintedTemplate_Helper_Locator')->getConverter($model)->getPdf();
        }

        return Mage::helper('Saas_PrintedTemplate_Helper_Locator')->getPdfAggregator()->render($pdfDocs);
    }
}
