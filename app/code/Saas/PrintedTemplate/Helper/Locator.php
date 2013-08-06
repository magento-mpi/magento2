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
 * Printed templates locator
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Helpers
 */
class Saas_PrintedTemplate_Helper_Locator extends Mage_Core_Helper_Abstract
{
    /**
     * Config path to name of HTML to PDF renderer class
     */
    const XML_PATH_LOCATOR = 'global/saas_printedtemplate/locator';

    /**
     * Get PDF convertor for specified model
     *
     * @param mixed $model
     * @param null|Saas_PrintedTemplate_Model_Template $template
     * @return boolean|Saas_PrintedTemplate_Model_Converter_PdfInterface
     * @throws Mage_Core_Exception
     */
    public function getConverter($model, Saas_PrintedTemplate_Model_Template $template = null)
    {
        $type = $this->_getModelType($model);
        if (!$type) {
            Mage::throwException(__('Cannot load converter for %1 model.', get_class($model)));
        }

        if (is_null($template)) {
            $templateId = Mage::getStoreConfig("sales_pdf/$type/printed_template", $model->getStoreId());
            $template = Mage::getModel('Saas_PrintedTemplate_Model_Template')
                ->loadForStore($templateId, $model->getStoreId())
                ->setEntityType($type);
        }
        $converter = Mage::getModel(
            'Saas_PrintedTemplate_Model_Converter_Template_' . ucfirst($type),
            array('data' => array('template' => $template, 'model' => $model))
        );
        if (!$converter) {
            Mage::throwException(__('Cannot load converter for %1 model.', $type));
        }

        return $converter;
    }

    /**
     * Get PDF renderer instance using class name from config
     *
     * @return Saas_PrintedTemplate_Model_Converter_PdfAdapter_Interface
     * @throws Mage_Core_Exception
     */
    public function getPdfRenderer()
    {
        $modelName = (string) Mage::getConfig()->getNode(self::XML_PATH_LOCATOR . '/pdf_renderer/class');
        $renderer = Mage::getModel($modelName);

        if (!$renderer) {
            Mage::throwException(__("PDF renderer adapter hasn't been found."));
        }

        if (!$renderer instanceof Saas_PrintedTemplate_Model_Converter_PdfAdapter_Interface) {
            Mage::throwException(__('Wrong type of PDF renderer adapter.'));
        }

        return $renderer;
    }

    /**
     * Get PDF aggregator instance using class name from config
     * This aggreagtor can assemble several PDF documents into one big document
     *
     * @return Saas_PrintedTemplate_Model_Converter_PdfAggregator_Interface
     * @throws Mage_Core_Exception
     */
    public function getPdfAggregator()
    {
        $modelName = (string) Mage::getConfig()->getNode(self::XML_PATH_LOCATOR . '/pdf_aggregator/class');
        $aggregator = Mage::getSingleton($modelName);

        if (!$aggregator) {
            Mage::throwException(__("PDF aggregator hasn't been found."));
        }

        if (!$aggregator instanceof Saas_PrintedTemplate_Model_Converter_PdfAdapter_PdfAggregatorInterface) {
            Mage::throwException(__('Wrong type of PDF aggregator.'));
        }

        return $aggregator;
    }

    /**
     * Create an object (decorator) with mock-up data for specified type
     *
     * @param string $type
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function getMockObjectByType($type)
    {
        $mock = Mage::getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_' . ucfirst($type));
        if (!$mock) {
            Mage::throwException(__("Cannot create mock-up data for preview."));
        }

        return $mock;
    }

    /**
     * Recognize converter type for specified model
     *
     * @param object $model
     * @return string
     */
    protected function _getModelType($model)
    {
        $elements = Mage::getConfig()->getNode(self::XML_PATH_LOCATOR . "/modelTypes");
        foreach ($elements->children() as $element) {
            $class = (string) $element;
            if ($model instanceof $class) {
                return $element->getName();
            }
        }

        return false;
    }
}
