<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Common sender
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Sender
{
    /** @var Magento_Core_Model_Email_Template_Mailer */
    protected $_mailer;

    /** @var Magento_Core_Model_Email_Info */
    protected $_emailInfo;

    /** @var Magento_Core_Model_Store */
    protected $_store;

    /**
     * @param Magento_Core_Model_Email_Template_Mailer $mailer
     * @param Magento_Core_Model_Email_Info $info
     * @param Magento_Core_Model_Store $store
     */
    public function __construct(Magento_Core_Model_Email_Template_Mailer $mailer,
        Magento_Core_Model_Email_Info $info, Magento_Core_Model_Store $store
    ) {
        $this->_mailer = $mailer;
        $this->_emailInfo = $info;
        $this->_store = $store;
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $template
     * @param string $sender
     * @param array $templateParams
     * @param int $storeId
     * @return Magento_Core_Model_Sender
     */
    public function send($email, $name, $template, $sender, $templateParams = array(), $storeId)
    {
        $this->_store->load($storeId);
        $this->_emailInfo->addTo($email, $name);
        $this->_mailer->addEmailInfo($this->_emailInfo);
        $this->_mailer->setSender($this->_store->getConfig($sender, $this->_store->getId()));
        $this->_mailer->setStoreId($this->_store->getId());
        $this->_mailer->setTemplateId($this->_store->getConfig($template, $this->_store->getId()));
        $this->_mailer->setTemplateParams($templateParams);
        $this->_mailer->send();
        return $this;
    }
}
