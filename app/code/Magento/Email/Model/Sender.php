<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Email
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Common sender
 *
 * @category   Magento
 * @package    Magento_Email
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Email\Model;

class Sender
{
    /** @var \Magento\Email\Model\Template\Mailer */
    protected $_mailer;

    /** @var \Magento\Email\Model\Info */
    protected $_emailInfo;

    /** @var \Magento\Core\Model\Store */
    protected $_store;

    /**
     * @param \Magento\Email\Model\Template\Mailer $mailer
     * @param \Magento\Email\Model\Info $info
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Email\Model\Template\Mailer $mailer,
        \Magento\Email\Model\Info $info,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_mailer = $mailer;
        $this->_emailInfo = $info;
        $this->_store = $storeManager->getStore();
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $template
     * @param string $sender
     * @param array $templateParams
     * @param int $storeId
     * @return \Magento\Email\Model\Sender
     */
    public function send($email, $name, $template, $sender, $templateParams, $storeId)
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
