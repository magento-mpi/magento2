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
 * Template model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Saas_PrintedTemplate_Model_Template extends Magento_Core_Model_Template
{
    /**
     * Configuration path for default printed templates
     *
     * @var string
     */
    const XML_PATH_TEMPLATE_PRINTED = 'global/template/printed';

    /**
     * Type of invoice printed template
     *
     * @var string
     */
    const ENTITY_TYPE_INVOICE = 'invoice';

    /**
     * Type of creditmemo printed templates
     *
     * @var string
     */
    const ENTITY_TYPE_CREDITMEMO = 'creditmemo';

    /**
     * Type of shipment printed templates
     *
     * @var string
     */
    const ENTITY_TYPE_SHIPMENT = 'shipment';

    /**
     * Module name
     *
     * @var string
     */
    const MODULE_NAME = 'Saas_PrintedTemplate';

    /**
     * Page size
     *
     * @var Saas_PrintedTemplate_Model_PageSize
     */
    protected $_pageSize;

    /**
     * Header height
     *
     * @var Zend_Measure_Length
     */
    protected $_headerHeight;

    /**
     * Footer height
     *
     * @var Zend_Measure_Length
     */
    protected $_footerHeight;

    /**
     * Template filter instance
     *
     * @var Magento_Core_Model_Email_Template_Filter
     */
    protected $_templateFilter;

    /**
     * Default templates which are stored in file system
     *
     * @var array
     */
    static protected $_defaultTemplates;

    /**
     * Model constructor. Initialize resource for data.
     */
    protected function _construct()
    {
        $this->_init('Saas_PrintedTemplate_Model_Resource_Template');
    }


    /**
     * Template type
     *
     * @return int
     */
    public function getType()
    {
        return self::TYPE_HTML;
    }

    /**
     * Validate template settings
     *
     * @return bool true if everything alright
     * @throws UnexpectedValueException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validate()
    {
        $footerHeight = $headerHeight = null;

        if (!$this->getPageSize()) {
            throw new UnexpectedValueException('Page size should be defined.');
        }

        if ($this->hasFooter() && $this->getFooter() != '' && !$this->getFooterAutoHeight()) {
            if (!$this->getFooterHeight() || $this->getFooterHeight()->getValue() < 0) {
                throw new UnexpectedValueException(
                    'Footer height should be a numeric value which is greater than zero.'
                );
            }
            $footerHeight = $this->_getStandardizedLengthValue($this->getFooterHeight());
        }

        if ($this->hasHeader() && $this->getHeader() != '' && !$this->getHeaderAutoHeight()) {
            if (!$this->getHeaderHeight() || $this->getHeaderHeight()->getValue() < 0) {
                throw new UnexpectedValueException(
                    'Header height should be a numeric value which is greater than zero.'
                );
            }
            $headerHeight = $this->_getStandardizedLengthValue($this->getHeaderHeight());
        }

        $pageHeight = $this->_getStandardizedLengthValue(
            $this->getPageOrientation() ==
                Saas_PrintedTemplate_Model_Converter_PdfAdapter_Interface::PAGE_ORIENTATION_LANDSCAPE
                ? $this->getPageSize()->getWidth()
                : $this->getPageSize()->getHeight()
        );

        if (($headerHeight || $footerHeight) && ($pageHeight <= $footerHeight + $headerHeight)) {
            throw new UnexpectedValueException(
                "The height of header and footer can not be greater than page height."
            );
        }

        return true;
    }

    /**
     * Returns standardized value of length
     *
     * @param Saas_PrintedTemplate_Model_RelativeLength|Zend_Measure_Length $length
     * @return mixed
     * @throws LogicException If page is not set for relatve height
     * @throws InvalidArgumentException if length has incorrect type
     */
    protected function _getStandardizedLengthValue($length)
    {
        $length = clone $length;
        if ($length instanceof Saas_PrintedTemplate_Model_RelativeLength) {
            $pageSize = $this->getPageSize();
            if (!$pageSize) {
                throw new LogicException("Cannot calculate standartized length without page size.");
            }
            $length = $length->getLength($pageSize->getHeight());
        }

        if (!$length instanceof Zend_Measure_Length) {
            throw new InvalidArgumentException("Incorrect length given.");
        }

        return $length->setType(Zend_Measure_Length::STANDARD)->getValue();
    }

    /**
     * Set page size of template
     *
     * @param Saas_PrintedTemplate_Model_PageSize|string $size
     * @return Saas_PrintedTemplate_Model_Template Self
     */
    public function setPageSize($size)
    {
        $this->_pageSize = ($size instanceof Saas_PrintedTemplate_Model_PageSize)
            ? $size
            : Mage::getSingleton('Saas_PrintedTemplate_Model_Source_PageSize')->getSizeByName($size);

        return $this->setData('page_size', $this->_pageSize->getName());
    }

    /**
     * Return page size
     *
     * @return Saas_PrintedTemplate_Model_PageSize|null
     * @throws InvalidArgumentException
     */
    public function getPageSize()
    {
        if ($this->_pageSize) {
            return $this->_pageSize;
        }
        if ($sizeName = $this->getData('page_size')) {
            return Mage::getSingleton('Saas_PrintedTemplate_Model_Source_PageSize')->getSizeByName($sizeName);
        }

        return null;
    }

    protected function _checkLengthType($value)
    {
        if (!$value instanceof Zend_Measure_Length && !$value instanceof Saas_PrintedTemplate_Model_RelativeLength) {
            throw new InvalidArgumentException(
                'Incorrect type of height; should be either Zend_Measure_Length or RelativeLength Model.'
            );
        }
    }

    /**
     * Sets height of header
     *
     * @param Zend_Measure_Length|Saas_PrintedTemplate_Model_RelativeLength $height
     * @return Saas_PrintedTemplate_Model_Template Self
     * @throws InvalidArgumentException
     */
    public function setHeaderHeight($height)
    {
        $this->_checkLengthType($height);

        $this->_headerHeight = $height;
        $this->setData('header_height', $height->getValue());
        $this->setData('header_height_measurement', $height->getType());

        return $this;
    }

    /**
     * Returns height of header
     *
     * @return Zend_Measure_Length|Saas_PrintedTemplate_Model_RelativeLength|null
     */
    public function getHeaderHeight()
    {
        if (!$this->_headerHeight && $this->hasData('header_height') && $this->hasData('header_height_measurement')) {
            if ($this->getData('header_height_measurement') == Saas_PrintedTemplate_Model_RelativeLength::LENGTH_TYPE) {
                $this->_headerHeight = Mage::getModel(
                    'Saas_PrintedTemplate_Model_RelativeLength',
                    array('percent' => $this->getData('footer_height'))
                );
            } else {
                $this->_headerHeight = new Zend_Measure_Length(
                    (float) $this->getData('header_height'),
                    $this->getData('header_height_measurement')
                );
            }
        }

        return $this->_headerHeight;
    }

    /**
     * Sets height of footer
     *
     * @param Zend_Measure_Length|Saas_PrintedTemplate_Model_RelativeLength $height
     * @return Saas_PrintedTemplate_Model_Template Self
     */
    public function setFooterHeight($height)
    {
        $this->_checkLengthType($height);

        $this->_footerHeight = $height;
        $this->setData('footer_height', $height->getValue());
        $this->setData('footer_height_measurement', $height->getType());

        return $this;
    }

    /**
     * Returns height of footer
     *
     * @return Zend_Measure_Length|Saas_PrintedTemplate_Model_RelativeLength|null
     */
    public function getFooterHeight()
    {
        if (!$this->_footerHeight && $this->hasData('footer_height') && $this->hasData('footer_height_measurement')) {
            if ($this->getData('footer_height_measurement') == Saas_PrintedTemplate_Model_RelativeLength::LENGTH_TYPE) {
                $this->_footerHeight = Mage::getModel(
                    'Saas_PrintedTemplate_Model_RelativeLength',
                    array('percent' => $this->getData('footer_height'))
                );
            } else {
                $this->_footerHeight = new Zend_Measure_Length(
                    (float) $this->getData('footer_height'),
                    $this->getData('footer_height_measurement')
                );
            }
        }

        return $this->_footerHeight;
    }

    /**
     * Render specified template
     *
     * @param int|string $templateId
     * @param int $storeId
     * @return string
     */
    public function loadForStore($templateId, $storeId = null)
    {
        if (($storeId === null) && $this->getDesignConfig()->getStore()) {
            $storeId = $this->getDesignConfig()->getStore();
        }

        if (is_numeric($templateId)) {
            $this->load($templateId);
        } else {
            $localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
            $this->loadDefault($templateId, $localeCode);
        }

        if (!$this->getId()) {
            throw new UnexpectedValueException(
                'Cannot load printed template; please ensure that template for the store of the order is selected.'
            );
        }

        return $this;
    }

    /**
     * Get filter object for template processing
     *
     * @return Magento_Core_Model_Email_Template_Filter
     */
    public function getTemplateFilter()
    {
        if (empty($this->_templateFilter)) {
            $this->_templateFilter = Mage::getModel('Mage_Widget_Model_Template_Filter');
        }

        return $this->_templateFilter;
    }


    /**
     * Process printed template header
     *
     * @param   array $variables
     * @throws  Exception
     * @return  string
     */
    public function getProcessedHeader(array $variables = array())
    {
        if (!$this->hasHeader()) {
            return '';
        }

        return $this->_getProcessedString($this->getHeader(), $variables);
    }

    /**
     * Process printed template footer
     *
     * @param   array $variables
     * @throws  Exception
     * @return  string
     */
    public function getProcessedFooter(array $variables = array())
    {
        if (!$this->hasFooter()) {
            return '';
        }

        return $this->_getProcessedString($this->getFooter(), $variables);
    }

    /**
     * Process printed template content
     *
     * @param   array $variables
     * @throws  Exception
     * @return  string
     */
    public function getProcessedContent(array $variables = array())
    {
        return $this->_getProcessedString($this->getContent(), $variables);
    }

    /**
     * Process template content, substitude variables.
     *
     * @param   string $string
     * @param   array $variables
     * @throws  Exception
     * @return  string
     */
    protected function _getProcessedString($string, array $variables = array())
    {
        return $this->getTemplateFilter()
            ->setStoreId($this->getDesignConfig()->getStore())
            ->setIncludeProcessor(array($this, 'getInclude'))
            ->setVariables($variables)
            ->filter($string);
    }


    /**
     * Retrive translated template file
     *
     * @param string $file
     * @param string $type
     * @param string $localeCode
     * @return string
     */
    public function getTemplateFile($file, $type, $localeCode=null)
    {
        $localeDir = Mage::getModuleDir('locale', self::MODULE_NAME);
        if (is_null($localeCode) || preg_match('/[^a-zA-Z_]/', $localeCode)) {
            $localeCode = Mage::app()->getLocale()->getLocaleCode();
        }

        $filePath = $localeDir  . DS . $localeCode . DS . 'template' . DS . $type . DS . $file;

        if (!file_exists($filePath)) { // If no template specified for this locale, use store default
            $filePath = $localeDir . DS
                . Mage::app()->getLocale()->getDefaultLocale()
                . DS . 'template' . DS . $type . DS . $file;
        }

        if (!file_exists($filePath)) {  // If no template specified as  store default locale, use en_US
            $filePath = $localeDir . DS
                . Magento_Core_Model_LocaleInterface::DEFAULT_LOCALE
                . DS . 'template' . DS . $type . DS . $file;
        }

        $ioAdapter = new Magento_Io_File();
        $ioAdapter->open();

        return (string) $ioAdapter->read($filePath);
    }

    /**
     * Load default printed template from locale translate
     *
     * @param string $templateId
     * @param string $locale
     */
    public function loadDefault($templateId, $locale = null)
    {
        $defaultTemplates = self::getDefaultTemplates();
        if (!isset($defaultTemplates[$templateId])) {
            return $this;
        }

        $data = &$defaultTemplates[$templateId];
        $templateText = $this->getTemplateFile($data['file'], 'printed', $locale);

        if (preg_match('/<!--@name\s*(.*?)\s*@-->/', $templateText, $matches)) {
            $this->setName($matches[1]);
            $templateText = str_replace($matches[0], '', $templateText);
        }

        // @todo: process different measurements
        if (preg_match('/<!--@header_height\s*(.*?)\s*@-->/', $templateText, $matches)) {
            $this->setHeaderHeight(new Zend_Measure_Length($matches[1], Zend_Measure_Length::MILLIMETER));
            $this->setHeaderAutoHeight(false);
            $templateText = str_replace($matches[0], '', $templateText);
        } else {
            $this->setHeaderAutoHeight(true);
        }

        // @todo: process different measurements
        if (preg_match('/<!--@footer_height\s*(.*?)\s*@-->/', $templateText, $matches)) {
            $this->setFooterHeight(new Zend_Measure_Length($matches[1], Zend_Measure_Length::MILLIMETER));
            $this->setFooterAutoHeight(false);
            $templateText = str_replace($matches[0], '', $templateText);
        } else {
            $this->setFooterAutoHeight(true);
        }

        if (preg_match('/<!--@vars\n((?:.)*?)\n@-->/us', $templateText, $matches)) {
            $this->setData('orig_template_variables', str_replace("\n", '', $matches[1]));
            $templateText = str_replace($matches[0], '', $templateText);
        }

        /**
         * Remove comment lines
         */
        $templateText = preg_replace('#\{\*.*\*\}#suU', '', $templateText);
        // @todo re-factor it: remove this dependency
        $this->_importContent($templateText);
        $this->setId($templateId);

        return $this;
    }

    /**
     * Parse and set template content
     *
     * @param string $templateText
     * @return Saas_PrintedTemplate_Model_Template
     */
    protected function _importContent($templateText)
    {
        $this->_getTemplateParser()->importContent($templateText, $this);
        return $this;
    }

    /**
     * Retrive default templates from config
     *
     * @return array
     */
    static public function getDefaultTemplates()
    {
        if (is_null(self::$_defaultTemplates)) {
            self::$_defaultTemplates = Mage::getConfig()->getNode(self::XML_PATH_TEMPLATE_PRINTED)->asArray();
        }

        return self::$_defaultTemplates;
    }

    /**
     * @static
     * @param array $data
     *
     */
    static public function setDefaultTemplates(array $data)
    {
        self::$_defaultTemplates = $data;
    }

    /**
     * Collect all system config pathes where current template is currently used
     *
     * @return array
     */
    public function getSystemConfigPathsWhereUsedCurrently()
    {
        if ($this->hasData('system_config_paths_where_used_currently')) {
            return $this->getData('system_config_paths_where_used_currently');
        }

        $templateId = $this->getId();
        if (!$templateId) {
            return array();
        }

        $config = Mage::getSingleton('Mage_Backend_Model_Config_Structure');
        $paths = $config->getFieldPathsByAttribute(
            'source_model',
            sprintf('Saas_PrintedTemplate_Model_Source_Template_%s', ucfirst($this->getEntityType()))
        );

        if (!is_array($paths)) {
            return array();
        }

        $configData = Mage::getModel('Magento_Core_Model_Config_Data')
            ->getCollection()
            ->addValueFilter($templateId)
            ->addFieldToFilter('path', array('in' => $paths));

        $this->setData('system_config_paths_where_used_currently', $configData);
        return $configData;
    }

    /**
     * Delete config settings that are set to current template id
     *
     * @return Saas_PrintedTemplate_Model_Template
     */
    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        foreach ($this->getSystemConfigPathsWhereUsedCurrently() as $configData) {
            $configData->delete();
        }
        return $this;
    }

    /**
     * @return Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser
     */
    protected function _getTemplateParser()
    {
        return Mage::getSingleton('Saas_PrintedTemplate_Model_Wysiwyg_TemplateParser');
    }
}
