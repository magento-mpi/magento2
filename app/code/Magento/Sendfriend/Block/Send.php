<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sendfriend\Block;

/**
 * Email to a Friend Block
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Send extends \Magento\View\Element\Template
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
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sendfriend\Helper\Data $sendfriendData
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sendfriend\Helper\Data $sendfriendData,
        \Magento\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_coreRegistry = $registry;
        $this->_sendfriendData = $sendfriendData;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
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
        $session = $this->_customerSession;

        if ($this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)) {
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
        $session = $this->_customerSession;

        if ($this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)) {
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
     * @return $this
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
        return $this->getUrl('*/*/sendmail', array('id' => $this->getProductId(), 'cat_id' => $this->getCategoryId()));
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
