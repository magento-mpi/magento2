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
 * Abstract converter model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
abstract class Saas_PrintedTemplate_Model_Converter_Template
    implements Saas_PrintedTemplate_Model_Converter_HtmlInterface, Saas_PrintedTemplate_Model_Converter_PdfInterface
{
    /**
     * Template object for preview
     *
     * @var Saas_PrintedTemplate_Model_Template
     */
    protected $_template = null;

    /**
     * Store ID for correct selecting template's locale
     *
     * @var int|null
     */
    protected $_storeId = null;

    /**
     * An associated array of varibales and their values which will be substituted in the template
     *
     * @var array
     */
    protected $_variables = array();

    /**
     * Rendered HTML code of template header
     *
     * @var string
     */
    protected $_headerHtml = null;

    /**
     * Rendered HTML code of template footer
     *
     * @var string
     */
    protected $_footerHtml = null;

    /**
     * Rendered HTML code of template content
     *
     * @var string
     */
    protected $_contentHtml = null;

    /**
     * Flag which indicate either template HTML has already been rendered or not
     *
     * @var boolean
     */
    protected $_isTemplateRendered = false;

    /**
     * Construct template model
     *
     * @param array $args Array with arguments and they should be:
     *     Saas_PrintedTemplate_Model_Template $template
     *     array $variables = array()
     *     int $storeId = null
     */
    public function __construct(array $args)
    {
        // Check arguments
        if (isset($args[0]) && $args[0] instanceof Saas_PrintedTemplate_Model_Template) {
            $this->_template = $args[0];
            $this->_storeId = isset($args[2]) ? $args[2] : null;
            $variables = isset($args[1]) ? $args[1] : array();
        } else {
            throw new InvalidArgumentException('The constructor\'s arguments are incorrect.');
        }

        $this->_initVariables($variables);
    }

    /**
     * Get Template Id
     *
     * @return int
     */
    public function getTemplateId()
    {
        return $this->_template->getId();
    }

    /**
     * Returns PDF representation of the document
     *
     * @return string
     * @throws Magento_Core_Exception
     */
    public function getPdf()
    {
        $pdfRenderer = Mage::helper('Saas_PrintedTemplate_Helper_Locator')->getPdfRenderer();

        if ($this->getHeaderHtml()) {
            $headerAutoHeight = $this->_template->getHeaderAutoHeight();
            if ($headerAutoHeight) {
                $pdfRenderer->setupHeader($this->getHeaderHtml());
            } else {
                $headerHeight = $this->_template->getHeaderHeight();
                if ($headerHeight instanceof Saas_PrintedTemplate_Model_RelativeLength) {
                    $pageSize = $this->_template->getPageSize();
                    $length = ($this->_template->getPageOrientation() ==
                        Saas_PrintedTemplate_Model_Converter_PdfAdapter_Interface::PAGE_ORIENTATION_LANDSCAPE)
                        ? $pageSize->getWidth()
                        : $pageSize->getHeight();
                    $headerHeight = $headerHeight->getLength($length);
                }
                $pdfRenderer->setupHeader($this->getHeaderHtml(), $headerHeight);
            }
        }

        if ($this->getFooterHtml()) {
            $footerAutoHeight = $this->_template->getFooterAutoHeight();
            if ($footerAutoHeight) {
                $pdfRenderer->setupFooter($this->getFooterHtml());
            } else {
                $footerHeight = $this->_template->getFooterHeight();
                if ($footerHeight instanceof Saas_PrintedTemplate_Model_RelativeLength) {
                    $pageSize = $this->_template->getPageSize();
                    $length = ($this->_template->getPageOrientation() ==
                        Saas_PrintedTemplate_Model_Converter_PdfAdapter_Interface::PAGE_ORIENTATION_LANDSCAPE)
                        ? $pageSize->getWidth()
                        : $pageSize->getHeight();
                    $footerHeight = $footerHeight->getLength($length);
                }
                $pdfRenderer->setupFooter($this->getFooterHtml(), $footerHeight);
            }
        }

        return $pdfRenderer->renderHtml(
            $this->getContentHtml(), $this->_template->getPageSize(), $this->_template->getPageOrientation()
        );
    }

    /**
     * Returns HTML representation of document's header
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        if (!$this->_isTemplateRendered) {
            $this->_renderHtml();
        }

        return $this->_headerHtml;
    }

    /**
     * Returns HTML representation of document's content
     *
     * @return string
     */
    public function getContentHtml()
    {
        if (!$this->_isTemplateRendered) {
            $this->_renderHtml();
        }

        return $this->_contentHtml;
    }

    /**
     * Returns HTML representation of document's footer
     *
     * @return string
     */
    public function getFooterHtml()
    {
        if (!$this->_isTemplateRendered) {
            $this->_renderHtml();
        }

        return $this->_footerHtml;
    }

    /**
     * Render all document HTML (header, content and footer)
     *
     * @return Saas_PrintedTemplate_Model_Converter_Template
     */
    protected function _renderHtml()
    {
        // emulate store locale if it is necessary
        if (!is_null($this->_storeId)) {
            Mage::app()->getLocale()->emulate($this->_storeId);
            Mage::app()->setCurrentStore($this->_storeId);
        }

        $this->_headerHtml = $this->_template->getProcessedHeader($this->_variables);
        $this->_contentHtml = $this->_template->getProcessedContent($this->_variables);
        $this->_footerHtml = $this->_template->getProcessedFooter($this->_variables);

        // revert store locale to current
        if (!is_null($this->_storeId)) {
            Mage::app()->getLocale()->revert();
        }

        $this->_isTemplateRendered = true;

        return $this;
    }

    /**
     * Initialize variable models
     *
     * @param array $variables
     * @return Saas_PrintedTemplate_Model_Converter_Template
     */
    protected function _initVariables(array $variables)
    {
        $variables['pagination'] = null; // add pagination variable
        $variables['config'] = null; // add config variable

        foreach ($variables as $name => $value) {
            $variableModel = $this->_getVariableModel($name, $value);
            if ($variableModel) {
                $this->_variables[$name] = $variableModel;
            }
        }

        return $this;
    }

    /**
     * Return variable container by name
     *
     * @param string $variableName
     * @param mixed value
     * @return Saas_PrintedTemplate_Model_Template_Variable_Abstract
     */
    protected function _getVariableModel($variableName, $value)
    {
        return Mage::getModel('Saas_PrintedTemplate_Model_Variable_' . uc_words($variableName),
            array('value' => $value)
        );
    }
}
