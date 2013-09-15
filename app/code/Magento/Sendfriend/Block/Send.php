<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Email to a Friend Block
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sendfriend\Block;

class Send extends \Magento\Core\Block\Template
{
    /**
     * Sendfriend data
     *
     * @var \Magento\Sendfriend\Helper\Data
     */
    protected $_sendfriendData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Sendfriend\Helper\Data $sendfriendData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Sendfriend\Helper\Data $sendfriendData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_sendfriendData = $sendfriendData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUserName()
    {
        $name = $this->getFormData()->getData('sender/name');
        if (!empty($name)) {
            return trim($name);
        }

        /* @var $session \Magento\Customer\Model\Session */
        $session = \Mage::getSingleton('Magento\Customer\Model\Session');

        if ($session->isLoggedIn()) {
            return $session->getCustomer()->getName();
        }

        return '';
    }

    /**
     * Retrieve sender email address
     *
     * @return string
     */
    public function getEmail()
    {
        $email = $this->getFormData()->getData('sender/email');
        if (!empty($email)) {
            return trim($email);
        }

        /* @var $session \Magento\Customer\Model\Session */
        $session = \Mage::getSingleton('Magento\Customer\Model\Session');

        if ($session->isLoggedIn()) {
            return $session->getCustomer()->getEmail();
        }

        return '';
    }

    /**
     * Retrieve Message text
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->getFormData()->getData('sender/message');
    }

    /**
     * Retrieve Form data or empty \Magento\Object
     *
     * @return \Magento\Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (!$data instanceof \Magento\Object) {
            $data = new \Magento\Object();
            $this->setData('form_data', $data);
        }

        return $data;
    }

    /**
     * Set Form data array
     *
     * @param array $data
     * @return \Magento\Sendfriend\Block\Send
     */
    public function setFormData($data)
    {
        if (is_array($data)) {
            $this->setData('form_data', new \Magento\Object($data));
        }

        return $this;
    }

    /**
     * Retrieve Current Product Id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->getRequest()->getParam('id', null);
    }

    /**
     * Retrieve current category id for product
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->getRequest()->getParam('cat_id', null);
    }

    /**
     * Retrieve Max Recipients
     *
     * @return int
     */
    public function getMaxRecipients()
    {
        return $this->_sendfriendData->getMaxRecipients();
    }

    /**
     * Retrieve Send URL for Form Action
     *
     * @return string
     */
    public function getSendUrl()
    {
        return \Mage::getUrl('*/*/sendmail', array(
            'id'     => $this->getProductId(),
            'cat_id' => $this->getCategoryId()
        ));
    }

    /**
     * Return send friend model
     *
     * @return \Magento\Sendfriend\Model\Sendfriend
     */
    protected function _getSendfriendModel()
    {
        return $this->_coreRegistry->registry('send_to_friend_model');
    }

    /**
     * Check if user is allowed to send
     *
     * @return boolean
     */
    public function canSend()
    {
        return !$this->_getSendfriendModel()->isExceedLimit();
    }
}
