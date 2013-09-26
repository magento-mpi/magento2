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
 * Newsletter subscribe block
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Newsletter_Block_Subscribe extends Magento_Core_Block_Template
{
    /**
     * Newsletter session
     *
     * @var Magento_Newsletter_Model_Session
     */
    protected $_newsletterSession;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Newsletter_Model_Session $newsletterSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Newsletter_Model_Session $newsletterSession,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_newsletterSession = $newsletterSession;
    }

    public function getSuccessMessage()
    {
        return $this->_newsletterSession->getSuccess();
    }

    public function getErrorMessage()
    {
        return $this->_newsletterSession->getError();
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new', array('_secure' => true));
    }
}
