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
namespace Magento\Core\Model;

class Sender
{
    /** @var \Magento\Core\Model\Email\Template\Mailer */
    protected $_mailer;

    /** @var \Magento\Core\Model\Email\Info */
    protected $_emailInfo;

    /** @var \Magento\Core\Model\Store */
    protected $_store;

    /**
     * @param \Magento\Core\Model\Email\Template\Mailer $mailer
     * @param \Magento\Core\Model\Email\Info $info
     * @param \Magento\Core\Model\Store $store
     */
    public function __construct(\Magento\Core\Model\Email\Template\Mailer $mailer,
        \Magento\Core\Model\Email\Info $info, \Magento\Core\Model\Store $store
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
     * @return \Magento\Core\Model\Sender
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
