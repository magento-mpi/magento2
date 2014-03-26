<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product send to friend block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @module     Catalog
 */
namespace Magento\Catalog\Block\Product;

class Send extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     * @param array $priceBlockTypes
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context,
            $data,
            $priceBlockTypes
        );
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->_customerSession->getCustomer()->getName();
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return (string)$this->_customerSession->getCustomer()->getEmail();
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->getRequest()->getParam('id');
    }

    /**
     * @return int
     */
    public function getMaxRecipients()
    {
        $sendToFriendModel = $this->_coreRegistry->registry('send_to_friend_model');
        return $sendToFriendModel->getMaxRecipients();
    }
}
