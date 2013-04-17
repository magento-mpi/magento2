<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Saas_Index_Model_System_Message_Decorator_IndexOutdated
{
    /**
     * @var Mage_Index_Model_System_Message_IndexOutdated
     */
    protected $_message;

    /**
     * @var Saas_Index_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Model_UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var Saas_Index_Model_Flag
     */
    protected $_flag;

    /**
     * @param Mage_Index_Model_System_Message_IndexOutdated $message
     * @param Saas_Index_Helper_Data $helper
     * @param Mage_Core_Model_UrlInterface $urlBuilder
     * @param Saas_Index_Model_FlagFactory $flagFactory
     */
    public function __construct(
        Mage_Index_Model_System_Message_IndexOutdated $message,
        Saas_Index_Helper_Data $helper,
        Mage_Core_Model_UrlInterface $urlBuilder,
        Saas_Index_Model_FlagFactory $flagFactory
    )
    {
        $this->_helper = $helper;
        $this->_urlBuilder = $urlBuilder;
        $this->_flag = $flagFactory->create();
        $this->_flag->loadSelf();
        $this->_message = $message;
    }

    /**
     * Check whether message should be displayed
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->_flag->isShowIndexNotification() && $this->_message->isDisplayed();
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->_message->getIdentity();
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return $this->_message->getSeverity();
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $url = $this->_urlBuilder->getUrl('adminhtml/process/list');
        return $this->_helper->__('You need to refresh the search index. Please click <a href="%s">here</a>.', $url);
    }
}
