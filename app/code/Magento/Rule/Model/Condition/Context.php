<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract block context object. Will be used as rule condition constructor modification point after release.
 * Important: Should not be modified by extension developers.
 */
class Magento_Rule_Model_Condition_Context implements Magento_ObjectManager_ContextInterface
{
    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Magento_Rule_Model_ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Rule_Model_ConditionFactory $conditionFactory
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_Layout $layout,
        Magento_Rule_Model_ConditionFactory $conditionFactory,
        Magento_Core_Model_Logger $logger
    ) {
        $this->_viewUrl = $viewUrl;
        $this->_locale = $locale;
        $this->_layout = $layout;
        $this->_conditionFactory = $conditionFactory;
        $this->_logger = $logger;
    }

    /**
     * @return Magento_Core_Model_View_Url
     */
    public function getViewUrl()
    {
        return $this->_viewUrl;
    }

    /**
     * @return Magento_Core_Model_LocaleInterface
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * @return Magento_Core_Model_Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @return Magento_Core_Model_Layout
     */
    public function getConditionFactory()
    {
        return $this->_conditionFactory;
    }

    /**
     * @return Magento_Core_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
