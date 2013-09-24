<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Template model
 *
 * @method Magento_Newsletter_Model_Resource_Template _getResource()
 * @method Magento_Newsletter_Model_Resource_Template getResource()
 * @method string getTemplateCode()
 * @method Magento_Newsletter_Model_Template setTemplateCode(string $value)
 * @method Magento_Newsletter_Model_Template setTemplateText(string $value)
 * @method Magento_Newsletter_Model_Template setTemplateTextPreprocessed(string $value)
 * @method string getTemplateStyles()
 * @method Magento_Newsletter_Model_Template setTemplateStyles(string $value)
 * @method int getTemplateType()
 * @method Magento_Newsletter_Model_Template setTemplateType(int $value)
 * @method string getTemplateSubject()
 * @method Magento_Newsletter_Model_Template setTemplateSubject(string $value)
 * @method string getTemplateSenderName()
 * @method Magento_Newsletter_Model_Template setTemplateSenderName(string $value)
 * @method string getTemplateSenderEmail()
 * @method Magento_Newsletter_Model_Template setTemplateSenderEmail(string $value)
 * @method int getTemplateActual()
 * @method Magento_Newsletter_Model_Template setTemplateActual(int $value)
 * @method string getAddedAt()
 * @method Magento_Newsletter_Model_Template setAddedAt(string $value)
 * @method string getModifiedAt()
 * @method Magento_Newsletter_Model_Template setModifiedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Newsletter_Model_Template extends Magento_Core_Model_Template
{
    /**
     * Template Text Preprocessed flag
     *
     * @var bool
     */
    protected $_preprocessFlag = false;

    /**
     * Mail object
     *
     * @var Zend_Mail
     */
    protected $_mail;

    /**
     * Store manager to emulate design
     *
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * Http-request, used to determine current store in multi-store mode
     *
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * Filter for newsletter text
     *
     * @var Magento_Newsletter_Model_Template_Filter
     */
    protected $_templateFilter;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Newsletter_Model_Template_Filter $filter
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Controller_Request_Http $request,
        Magento_Newsletter_Model_Template_Filter $filter,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_filter = $filter;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($design, $context, $registry, $data);
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Newsletter_Model_Resource_Template');
    }

    /**
     * Validate Newsletter template
     *
     * @throws Magento_Core_Exception
     * @return bool
     */
    public function validate()
    {
        $validators = array(
            'template_code'         => array(Zend_Filter_Input::ALLOW_EMPTY => false),
            'template_type'         => 'Int',
            'template_sender_email' => 'EmailAddress',
            'template_sender_name'  => array(Zend_Filter_Input::ALLOW_EMPTY => false)
        );
        $data = array();
        foreach (array_keys($validators) as $validateField) {
            $data[$validateField] = $this->getDataUsingMethod($validateField);
        }

        $validateInput = new Zend_Filter_Input(array(), $validators, $data);
        if (!$validateInput->isValid()) {
            $errorMessages = array();
            foreach ($validateInput->getMessages() as $messages) {
                if (is_array($messages)) {
                    foreach ($messages as $message) {
                        $errorMessages[] = $message;
                    }
                } else {
                    $errorMessages[] = $messages;
                }
            }

            Mage::throwException(join("\n", $errorMessages));
        }
    }

    /**
     * Processing object before save data
     *
     * @return Magento_Newsletter_Model_Template
     */
    protected function _beforeSave()
    {
        $this->validate();
        return parent::_beforeSave();
    }

    /**
     * Load template by code
     *
     * @param string $templateCode
     * @return Magento_Newsletter_Model_Template
     */
    public function loadByCode($templateCode)
    {
        $this->_getResource()->loadByCode($this, $templateCode);
        return $this;
    }

    /**
     * Getter for template type
     *
     * @return int|string
     */
    public function getType()
    {
        return $this->getTemplateType();
    }

    /**
     * Check is Preprocessed
     *
     * @return bool
     */
    public function isPreprocessed()
    {
        return strlen($this->getTemplateTextPreprocessed()) > 0;
    }

    /**
     * Check Template Text Preprocessed
     *
     * @return bool
     */
    public function getTemplateTextPreprocessed()
    {
        if ($this->_preprocessFlag) {
            $this->setTemplateTextPreprocessed($this->getProcessedTemplate());
        }

        return $this->getData('template_text_preprocessed');
    }

    /**
     * Retrieve processed template
     *
     * @param array $variables
     * @param bool $usePreprocess
     * @return string
     */
    public function getProcessedTemplate(array $variables = array(), $usePreprocess = false)
    {
        if (!$this->_preprocessFlag) {
            $variables['this'] = $this;
        }

        if ($this->_storeManager->hasSingleStore()) {
            $this->_filter->setStoreId($this->_storeManager->getStore()->getId());
        } else {
            $this->_filter->setStoreId($this->_request->getParam('store_id'));
        }

        $this->_filter
            ->setIncludeProcessor(array($this, 'getInclude'))
            ->setVariables($variables);

        if ($usePreprocess && $this->isPreprocessed()) {
            return $this->_filter->filter($this->getPreparedTemplateText(true));
        }

        return $this->_filter->filter($this->getPreparedTemplateText());
    }

    /**
     * Makes additional text preparations for HTML templates
     *
     * @param bool $usePreprocess Use Preprocessed text or original text
     * @return string
     */
    public function getPreparedTemplateText($usePreprocess = false)
    {
        $text = $usePreprocess ? $this->getTemplateTextPreprocessed() : $this->getTemplateText();

        if ($this->_preprocessFlag || $this->isPlain() || !$this->getTemplateStyles()) {
            return $text;
        }
        // wrap styles into style tag
        $html = "<style type=\"text/css\">\n%s\n</style>\n%s";
        return sprintf($html, $this->getTemplateStyles(), $text);
    }

    /**
     * Retrieve included template
     *
     * @param string $templateCode
     * @param array $variables
     * @return string
     */
    public function getInclude($templateCode, array $variables)
    {
        return Mage::getModel('Magento_Newsletter_Model_Template')
            ->loadByCode($templateCode)
            ->getProcessedTemplate($variables);
    }

    /**
     * Retrieve processed template subject
     *
     * @param array $variables
     * @return string
     */
    public function getProcessedTemplateSubject(array $variables)
    {
        $processor = new Magento_Filter_Template();

        if (!$this->_preprocessFlag) {
            $variables['this'] = $this;
        }

        $processor->setVariables($variables);
        return $processor->filter($this->getTemplateSubject());
    }

    /**
     * Retrieve template text wrapper
     *
     * @return string
     */
    public function getTemplateText()
    {
        if (!$this->getData('template_text') && !$this->getId()) {
            $this->setData('template_text',
                __('Follow this link to unsubscribe <!-- This tag is for unsubscribe link  -->'
                    . '<a href="{{var subscriber.getUnsubscriptionLink()}}">{{var subscriber.getUnsubscriptionLink()}}'
                    . '</a>'));
        }

        return $this->getData('template_text');
    }

    /**
     * Check if template can be added to newsletter queue
     *
     * @return boolean
     */
    public function isValidForSend()
    {
        return !$this->_coreStoreConfig->getConfigFlag(Magento_Core_Helper_Data::XML_PATH_SYSTEM_SMTP_DISABLE)
            && $this->getTemplateSenderName()
            && $this->getTemplateSenderEmail()
            && $this->getTemplateSubject();
    }
}
