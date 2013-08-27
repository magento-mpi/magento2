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
 * Subscriber model
 *
 * @method Magento_Newsletter_Model_Resource_Subscriber _getResource()
 * @method Magento_Newsletter_Model_Resource_Subscriber getResource()
 * @method int getStoreId()
 * @method Magento_Newsletter_Model_Subscriber setStoreId(int $value)
 * @method string getChangeStatusAt()
 * @method Magento_Newsletter_Model_Subscriber setChangeStatusAt(string $value)
 * @method int getCustomerId()
 * @method Magento_Newsletter_Model_Subscriber setCustomerId(int $value)
 * @method string getSubscriberEmail()
 * @method Magento_Newsletter_Model_Subscriber setSubscriberEmail(string $value)
 * @method int getSubscriberStatus()
 * @method Magento_Newsletter_Model_Subscriber setSubscriberStatus(int $value)
 * @method string getSubscriberConfirmCode()
 * @method Magento_Newsletter_Model_Subscriber setSubscriberConfirmCode(string $value)
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Newsletter_Model_Subscriber extends Magento_Core_Model_Abstract
{
    const STATUS_SUBSCRIBED     = 1;
    const STATUS_NOT_ACTIVE     = 2;
    const STATUS_UNSUBSCRIBED   = 3;
    const STATUS_UNCONFIRMED    = 4;

    const XML_PATH_CONFIRM_EMAIL_TEMPLATE       = 'newsletter/subscription/confirm_email_template';
    const XML_PATH_CONFIRM_EMAIL_IDENTITY       = 'newsletter/subscription/confirm_email_identity';
    const XML_PATH_SUCCESS_EMAIL_TEMPLATE       = 'newsletter/subscription/success_email_template';
    const XML_PATH_SUCCESS_EMAIL_IDENTITY       = 'newsletter/subscription/success_email_identity';
    const XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE   = 'newsletter/subscription/un_email_template';
    const XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY   = 'newsletter/subscription/un_email_identity';
    const XML_PATH_CONFIRMATION_FLAG            = 'newsletter/subscription/confirm';
    const XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG   = 'newsletter/subscription/allow_guest_subscribe';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'newsletter_subscriber';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'subscriber';

    /**
     * True if data changed
     *
     * @var bool
     */
    protected $_isStatusChanged = false;

    /**
     * Newsletter data
     *
     * @var Magento_Newsletter_Helper_Data
     */
    protected $_newsletterData = null;

    /**
     * @param Magento_Newsletter_Helper_Data $newsletterData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Newsletter_Helper_Data $newsletterData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_newsletterData = $newsletterData;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Newsletter_Model_Resource_Subscriber');
    }

    /**
     * Alias for getSubscriberId()
     *
     * @return int
     */
    public function getId()
    {
        return $this->getSubscriberId();
    }

    /**
     * Alias for setSubscriberId()
     *
     * @param int $value
     */
    public function setId($value)
    {
        return $this->setSubscriberId($value);
    }

    /**
     * Alias for getSubscriberConfirmCode()
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getSubscriberConfirmCode();
    }

    /**
     * Return link for confirmation of subscription
     *
     * @return string
     */
    public function getConfirmationLink() {
        return $this->_newsletterData->getConfirmationUrl($this);
    }

    /**
     * Returns Insubscribe url
     *
     * @return string
     */
    public function getUnsubscriptionLink() {
        return $this->_newsletterData->getUnsubscribeUrl($this);
    }

    /**
     * Alias for setSubscriberConfirmCode()
     *
     * @param string $value
     */
    public function setCode($value)
    {
        return $this->setSubscriberConfirmCode($value);
    }

    /**
     * Alias for getSubscriberStatus()
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getSubscriberStatus();
    }

    /**
     * Alias for setSubscriberStatus()
     *
     * @param int
     */
    public function setStatus($value)
    {
        return $this->setSubscriberStatus($value);
    }

    /**
     * Set the error messages scope for subscription
     *
     * @param boolean $scope
     * @return Magento_Newsletter_Model_Subscriber
     */

    public function setMessagesScope($scope)
    {
        $this->getResource()->setMessagesScope($scope);
        return $this;
    }

    /**
     * Alias for getSubscriberEmail()
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getSubscriberEmail();
    }

    /**
     * Alias for setSubscriberEmail()
     *
     * @param string $value
     */
    public function setEmail($value)
    {
        return $this->setSubscriberEmail($value);
    }

    /**
     * Set for status change flag
     *
     * @param boolean $value
     */
    public function setIsStatusChanged($value)
    {
        $this->_isStatusChanged = (boolean) $value;
           return $this;
    }

    /**
     * Return status change flag value
     *
     * @return boolean
     */
    public function getIsStatusChanged()
    {
        return $this->_isStatusChanged;
    }

    /**
     * Return customer subscription status
     *
     * @return bool
     */
    public function isSubscribed()
    {
        if($this->getId() && $this->getStatus()==self::STATUS_SUBSCRIBED) {
            return true;
        }

        return false;
    }


     /**
     * Load subscriber data from resource model by email
     *
     * @param int $subscriberId
     */
    public function loadByEmail($subscriberEmail)
    {
        $this->addData($this->getResource()->loadByEmail($subscriberEmail));
        return $this;
    }

    /**
     * Load subscriber info by customer
     *
     * @param Magento_Customer_Model_Customer $customer
     * @return Magento_Newsletter_Model_Subscriber
     */
    public function loadByCustomer(Magento_Customer_Model_Customer $customer)
    {
        $data = $this->getResource()->loadByCustomer($customer);
        $this->addData($data);
        if (!empty($data) && $customer->getId() && !$this->getCustomerId()) {
            $this->setCustomerId($customer->getId());
            $this->setSubscriberConfirmCode($this->randomSequence());
            if ($this->getStatus()==self::STATUS_NOT_ACTIVE) {
                $this->setStatus($customer->getIsSubscribed() ? self::STATUS_SUBSCRIBED : self::STATUS_UNSUBSCRIBED);
            }
            $this->save();
        }
        return $this;
    }

    /**
     * Returns sting of random chars
     *
     * @param int $length
     * @return string
     */
    public function randomSequence($length=32)
    {
        $id = '';
        $par = array();
        $char = array_merge(range('a','z'),range(0,9));
        $charLen = count($char)-1;
        for ($i=0;$i<$length;$i++){
            $disc = mt_rand(0, $charLen);
            $par[$i] = $char[$disc];
            $id = $id.$char[$disc];
        }
        return $id;
    }

    /**
     * Subscribes by email
     *
     * @param string $email
     * @throws Exception
     * @return int
     */
    public function subscribe($email)
    {
        $this->loadByEmail($email);
        $customerSession = Mage::getSingleton('Magento_Customer_Model_Session');

        if(!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed   = (Mage::getStoreConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
        $isOwnSubscribes = false;
        $ownerId = Mage::getModel('Magento_Customer_Model_Customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email)
            ->getId();
        $isSubscribeOwnEmail = $customerSession->isLoggedIn() && $ownerId == $customerSession->getId();

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED
            || $this->getStatus() == self::STATUS_NOT_ACTIVE
        ) {
            if ($isConfirmNeed === true) {
                // if user subscribes own login email - confirmation is not needed
                $isOwnSubscribes = $isSubscribeOwnEmail;
                if ($isOwnSubscribes == true){
                    $this->setStatus(self::STATUS_SUBSCRIBED);
                } else {
                    $this->setStatus(self::STATUS_NOT_ACTIVE);
                }
            } else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }
            $this->setSubscriberEmail($email);
        }

        if ($isSubscribeOwnEmail) {
            $this->setStoreId($customerSession->getCustomer()->getStoreId());
            $this->setCustomerId($customerSession->getCustomerId());
        } else {
            $this->setStoreId(Mage::app()->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setIsStatusChanged(true);

        try {
            $this->save();
            if ($isConfirmNeed === true
                && $isOwnSubscribes === false
            ) {
                $this->sendConfirmationRequestEmail();
            } else {
                $this->sendConfirmationSuccessEmail();
            }

            return $this->getStatus();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Unsubscribes loaded subscription
     *
     */
    public function unsubscribe()
    {
        if ($this->hasCheckCode() && $this->getCode() != $this->getCheckCode()) {
            Mage::throwException(__('This is an invalid subscription confirmation code.'));
        }

        $this->setSubscriberStatus(self::STATUS_UNSUBSCRIBED)
            ->save();
        $this->sendUnsubscriptionEmail();
        return $this;
    }

    /**
     * Saving customer subscription status
     *
     * @param   Magento_Customer_Model_Customer $customer
     * @return  Magento_Newsletter_Model_Subscriber
     */
    public function subscribeCustomer($customer)
    {
        $this->loadByCustomer($customer);

        if ($customer->getImportMode()) {
            $this->setImportMode(true);
        }

        if (!$customer->getIsSubscribed() && !$this->getId()) {
            // If subscription flag not set or customer is not a subscriber
            // and no subscribe below
            return $this;
        }

        if(!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

       /*
        * Logical mismatch between customer registration confirmation code and customer password confirmation
        */
       $confirmation = null;
       if ($customer->isConfirmationRequired() && ($customer->getConfirmation() != $customer->getPassword())) {
           $confirmation = $customer->getConfirmation();
       }

        $sendInformationEmail = false;
        if ($customer->hasIsSubscribed()) {
            $status = $customer->getIsSubscribed()
                ? (!is_null($confirmation) ? self::STATUS_UNCONFIRMED : self::STATUS_SUBSCRIBED)
                : self::STATUS_UNSUBSCRIBED;
            /**
             * If subscription status has been changed then send email to the customer
             */
            if ($status != self::STATUS_UNCONFIRMED && $status != $this->getStatus()) {
                $sendInformationEmail = true;
            }
        } elseif (($this->getStatus() == self::STATUS_UNCONFIRMED) && (is_null($confirmation))) {
            $status = self::STATUS_SUBSCRIBED;
            $sendInformationEmail = true;
        } else {
            $status = ($this->getStatus() == self::STATUS_NOT_ACTIVE ? self::STATUS_UNSUBSCRIBED : $this->getStatus());
        }

        if($status != $this->getStatus()) {
            $this->setIsStatusChanged(true);
        }

        $this->setStatus($status);

        if(!$this->getId()) {
            $storeId = $customer->getStoreId();
            if ($customer->getStoreId() == 0) {
                $storeId = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
            }
            $this->setStoreId($storeId)
                ->setCustomerId($customer->getId())
                ->setEmail($customer->getEmail());
        } else {
            $this->setStoreId($customer->getStoreId())
                ->setEmail($customer->getEmail());
        }

        $this->save();
        $sendSubscription = $customer->getData('sendSubscription') || $sendInformationEmail;
        if (is_null($sendSubscription) xor $sendSubscription) {
            if ($this->getIsStatusChanged() && $status == self::STATUS_UNSUBSCRIBED) {
                $this->sendUnsubscriptionEmail();
            } elseif ($this->getIsStatusChanged() && $status == self::STATUS_SUBSCRIBED) {
                $this->sendConfirmationSuccessEmail();
            }
        }
        return $this;
    }

    /**
     * Confirms subscriber newsletter
     *
     * @param string $code
     * @return boolean
     */
    public function confirm($code)
    {
        if($this->getCode()==$code) {
            $this->setStatus(self::STATUS_SUBSCRIBED)
                ->setIsStatusChanged(true)
                ->save();
            return true;
        }

        return false;
    }

    /**
     * Mark receiving subscriber of queue newsletter
     *
     * @param  Magento_Newsletter_Model_Queue $queue
     * @return boolean
     */
    public function received(Magento_Newsletter_Model_Queue $queue)
    {
        $this->getResource()->received($this,$queue);
        return $this;
    }

    /**
     * Sends out confirmation email
     *
     * @return Magento_Newsletter_Model_Subscriber
     */
    public function sendConfirmationRequestEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }

        if(!Mage::getStoreConfig(self::XML_PATH_CONFIRM_EMAIL_TEMPLATE)
           || !Mage::getStoreConfig(self::XML_PATH_CONFIRM_EMAIL_IDENTITY)
        )  {
            return $this;
        }

        $translate = Mage::getSingleton('Magento_Core_Model_Translate');
        /* @var $translate Magento_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $email = Mage::getModel('Magento_Core_Model_Email_Template');

        $email->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_CONFIRM_EMAIL_TEMPLATE),
            Mage::getStoreConfig(self::XML_PATH_CONFIRM_EMAIL_IDENTITY),
            $this->getEmail(),
            $this->getName(),
            array('subscriber'=>$this)
        );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Sends out confirmation success email
     *
     * @return Magento_Newsletter_Model_Subscriber
     */
    public function sendConfirmationSuccessEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }

        if(!Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_TEMPLATE)
           || !Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_IDENTITY)
        ) {
            return $this;
        }

        $translate = Mage::getSingleton('Magento_Core_Model_Translate');
        /* @var $translate Magento_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $email = Mage::getModel('Magento_Core_Model_Email_Template');

        $email->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_TEMPLATE),
            Mage::getStoreConfig(self::XML_PATH_SUCCESS_EMAIL_IDENTITY),
            $this->getEmail(),
            $this->getName(),
            array('subscriber'=>$this)
        );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Sends out unsubsciption email
     *
     * @return Magento_Newsletter_Model_Subscriber
     */
    public function sendUnsubscriptionEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }
        if(!Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE)
           || !Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY)
        ) {
            return $this;
        }

        $translate = Mage::getSingleton('Magento_Core_Model_Translate');
        /* @var $translate Magento_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $email = Mage::getModel('Magento_Core_Model_Email_Template');

        $email->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE),
            Mage::getStoreConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY),
            $this->getEmail(),
            $this->getName(),
            array('subscriber'=>$this)
        );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Retrieve Subscribers Full Name if it was set
     *
     * @return string|null
     */
    public function getSubscriberFullName()
    {
        $name = null;
        if ($this->hasCustomerFirstname() || $this->hasCustomerLastname()) {
            $name = $this->getCustomerFirstname() . ' ' . $this->getCustomerLastname();
        }
        return $name;
    }
}
