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
     * Logger instance
     *
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_View_Url $viewUrl
     */
    public function __construct(Magento_Core_Model_Logger $logger, Magento_Core_Model_View_Url $viewUrl)
    {
        $this->_viewUrl = $viewUrl;
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
     * Get logger instance
     *
     * @return Magento_Core_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
