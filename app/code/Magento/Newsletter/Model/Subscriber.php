<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Model;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Exception\NoSuchEntityException;

/**
 * Subscriber model
 *
 * @method \Magento\Newsletter\Model\Resource\Subscriber _getResource()
 * @method \Magento\Newsletter\Model\Resource\Subscriber getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getChangeStatusAt()
 * @method $this setChangeStatusAt(string $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getSubscriberEmail()
 * @method $this setSubscriberEmail(string $value)
 * @method int getSubscriberStatus()
 * @method $this setSubscriberStatus(int $value)
 * @method string getSubscriberConfirmCode()
 * @method $this setSubscriberConfirmCode(string $value)
 * @method int getSubscriberId()
 * @method Subscriber setSubscriberId(int $value)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class Subscriber extends \Magento\Model\AbstractModel
{
    const STATUS_SUBSCRIBED = 1;
    const STATUS_NOT_ACTIVE = 2;
    const STATUS_UNSUBSCRIBED = 3;
    const STATUS_UNCONFIRMED = 4;

    const XML_PATH_CONFIRM_EMAIL_TEMPLATE = 'newsletter/subscription/confirm_email_template';
    const XML_PATH_CONFIRM_EMAIL_IDENTITY = 'newsletter/subscription/confirm_email_identity';
    const XML_PATH_SUCCESS_EMAIL_TEMPLATE = 'newsletter/subscription/success_email_template';
    const XML_PATH_SUCCESS_EMAIL_IDENTITY = 'newsletter/subscription/success_email_identity';
    const XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE = 'newsletter/subscription/un_email_template';
    const XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY = 'newsletter/subscription/un_email_identity';
    const XML_PATH_CONFIRMATION_FLAG = 'newsletter/subscription/confirm';
    const XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG = 'newsletter/subscription/allow_guest_subscribe';

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
     * @var \Magento\Newsletter\Helper\Data
     */
    protected $_newsletterData = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customer account service
     *
     * @var CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * Construct
     *
     * @param \Magento\Model\Context                    $context
     * @param \Magento\Registry                         $registry
     * @param \Magento\Newsletter\Helper\Data           $newsletterData
     * @param \Magento\Core\Model\Store\Config          $coreStoreConfig
     * @param \Magento\Mail\Template\TransportBuilder   $transportBuilder
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session           $customerSession
     * @param CustomerAccountServiceInterface           $customerAccountService
     * @param \Magento\Translate\Inline\StateInterface  $inlineTranslation
     * @param \Magento\Model\Resource\AbstractResource  $resource
     * @param \Magento\Data\Collection\Db               $resourceCollection
     * @param array                                     $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Newsletter\Helper\Data $newsletterData,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        CustomerAccountServiceInterface $customerAccountService,
        \Magento\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = []
    ) {
        $this->_newsletterData = $newsletterData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_customerAccountService = $customerAccountService;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Newsletter\Model\Resource\Subscriber');
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
     * @return $this
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
    public function getConfirmationLink()
    {
        return $this->_newsletterData->getConfirmationUrl($this);
    }

    /**
     * Returns Unsubscribe url
     *
     * @return string
     */
    public function getUnsubscriptionLink()
    {
        return $this->_newsletterData->getUnsubscribeUrl($this);
    }

    /**
     * Alias for setSubscriberConfirmCode()
     *
     * @param string $value
     * @return $this
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
     * @param int $value
     * @return $this
     */
    public function setStatus($value)
    {
        return $this->setSubscriberStatus($value);
    }

    /**
     * Set the error messages scope for subscription
     *
     * @param boolean $scope
     * @return $this
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
     * @return $this
     */
    public function setEmail($value)
    {
        return $this->setSubscriberEmail($value);
    }

    /**
     * Set for status change flag
     *
     * @param boolean $value
     * @return $this
     */
    public function setStatusChanged($value)
    {
        $this->_isStatusChanged = (boolean) $value;
        return $this;
    }

    /**
     * Return status change flag value
     *
     * @return boolean
     */
    public function isStatusChanged()
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
        if ($this->getId() && $this->getStatus() == self::STATUS_SUBSCRIBED) {
            return true;
        }

        return false;
    }

    /**
     * Load subscriber data from resource model by email
     *
     * @param string $subscriberEmail
     * @return $this
     */
    public function loadByEmail($subscriberEmail)
    {
        $this->addData($this->getResource()->loadByEmail($subscriberEmail));
        return $this;
    }

    /**
     * Load subscriber info by customerId
     *
     * @param int $customerId
     * @return $this
     */
    public function loadByCustomerId($customerId)
    {
        try {
            /** @var \Magento\Customer\Service\V1\Data\Customer $customerData */
            $customerData = $this->_customerAccountService->getCustomer($customerId);
            $data = $this->getResource()->loadByCustomerData($customerData);
            $this->addData($data);
            if (!empty($data) && $customerData->getId() && !$this->getCustomerId()) {
                $this->setCustomerId($customerData->getId());
                $this->setSubscriberConfirmCode($this->randomSequence());
                $this->save();
            }
        } catch (\Magento\Exception\NoSuchEntityException $e) {
        }
        return $this;
    }

    /**
     * Returns string of random chars
     *
     * @param int $length
     * @return string
     */
    public function randomSequence($length = 32)
    {
        $id = '';
        $par = array();
        $char = array_merge(range('a', 'z'), range(0, 9));
        $charLen = count($char) - 1;
        for ($i = 0; $i < $length; $i++) {
            $disc = mt_rand(0, $charLen);
            $par[$i] = $char[$disc];
            $id = $id . $char[$disc];
        }
        return $id;
    }

    /**
     * Subscribes by email
     *
     * @param string $email
     * @throws \Exception
     * @return int
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function subscribe($email)
    {
        $this->loadByEmail($email);

        if (!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed = ($this->_coreStoreConfig->getConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
        $isOwnSubscribes = false;

        $isSubscribeOwnEmail = $this->_customerSession->isLoggedIn()
            && $this->_customerSession->getCustomerDataObject()->getEmail() == $email;

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED
            || $this->getStatus() == self::STATUS_NOT_ACTIVE
        ) {
            if ($isConfirmNeed === true) {
                // if user subscribes own login email - confirmation is not needed
                $isOwnSubscribes = $isSubscribeOwnEmail;
                if ($isOwnSubscribes == true) {
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
            try {
                $customer = $this->_customerAccountService->getCustomer($this->_customerSession->getCustomerId());
                $this->setStoreId($customer->getStoreId());
                $this->setCustomerId($customer->getId());
            } catch(NoSuchEntityException $e) {
                $this->setStoreId($this->_storeManager->getStore()->getId());
                $this->setCustomerId(0);
            }
        } else {
            $this->setStoreId($this->_storeManager->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setStatusChanged(true);

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
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Unsubscribes loaded subscription
     *
     * @throws \Magento\Model\Exception
     * @return $this
     */
    public function unsubscribe()
    {
        if ($this->hasCheckCode() && $this->getCode() != $this->getCheckCode()) {
            throw new \Magento\Model\Exception(__('This is an invalid subscription confirmation code.'));
        }

        if ($this->getSubscriberStatus() != self::STATUS_UNSUBSCRIBED) {
            $this->setSubscriberStatus(self::STATUS_UNSUBSCRIBED)->save();
            $this->sendUnsubscriptionEmail();
        }
        return $this;
    }

    /**
     * Subscribe the customer with the id provided
     *
     * @param int $customerId
     * @return $this
     */
    public function subscribeCustomerById($customerId)
    {
        return $this->_updateCustomerSubscription($customerId, true);
    }

    /**
     * unsubscribe the customer with the id provided
     *
     * @param int $customerId
     * @return $this
     */
    public function unsubscribeCustomerById($customerId)
    {
        return $this->_updateCustomerSubscription($customerId, false);
    }

    /**
     * Update the subscription based on latest information of associated customer.
     *
     * @param int $customerId
     * @return $this
     */
    public function updateSubscription($customerId)
    {
        $this->loadByCustomerId($customerId);
        $this->_updateCustomerSubscription($customerId, $this->isSubscribed());
        return $this;
    }

    /**
     * Saving customer subscription status
     *
     * @param int $customerId
     * @param bool $subscribe indicates whether the customer should be subscribed or unsubscribed
     * @return  $this
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _updateCustomerSubscription($customerId, $subscribe)
    {
        try {
            $customerData = $this->_customerAccountService->getCustomer($customerId);
        } catch (NoSuchEntityException $e) {
            return $this;
        }

        $this->loadByCustomerId($customerId);
        if (!$subscribe && !$this->getId()) {
            return $this;
        }

        if (!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $sendInformationEmail = false;
        $status = self::STATUS_SUBSCRIBED;
        if ($subscribe) {
            if (CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_REQUIRED
                == $this->_customerAccountService->getConfirmationStatus($customerId)
            ) {
                $status = self::STATUS_UNCONFIRMED;
            }
        } else {
            $status = self::STATUS_UNSUBSCRIBED;
        }
        /**
         * If subscription status has been changed then send email to the customer
         */
        if ($status != self::STATUS_UNCONFIRMED && $status != $this->getStatus()) {
            $sendInformationEmail = true;
        }

        if ($status != $this->getStatus()) {
            $this->setStatusChanged(true);
        }

        $this->setStatus($status);

        if (!$this->getId()) {
            $storeId = $customerData->getStoreId();
            if ($customerData->getStoreId() == 0) {
                $storeId = $this->_storeManager->getWebsite($customerData->getWebsiteId())->getDefaultStore()->getId();
            }
            $this->setStoreId($storeId)
                ->setCustomerId($customerData->getId())
                ->setEmail($customerData->getEmail());
        } else {
            $this->setStoreId($customerData->getStoreId())
                ->setEmail($customerData->getEmail());
        }

        $this->save();
        $sendSubscription = $sendInformationEmail;
        if (is_null($sendSubscription) xor $sendSubscription) {
            if ($this->isStatusChanged() && $status == self::STATUS_UNSUBSCRIBED) {
                $this->sendUnsubscriptionEmail();
            } elseif ($this->isStatusChanged() && $status == self::STATUS_SUBSCRIBED) {
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
        if ($this->getCode() == $code) {
            $this->setStatus(self::STATUS_SUBSCRIBED)
                ->setStatusChanged(true)
                ->save();
            return true;
        }

        return false;
    }

    /**
     * Mark receiving subscriber of queue newsletter
     *
     * @param  \Magento\Newsletter\Model\Queue $queue
     * @return boolean
     */
    public function received(\Magento\Newsletter\Model\Queue $queue)
    {
        $this->getResource()->received($this, $queue);
        return $this;
    }

    /**
     * Sends out confirmation email
     *
     * @return $this
     */
    public function sendConfirmationRequestEmail()
    {
        if (!$this->_coreStoreConfig->getConfig(self::XML_PATH_CONFIRM_EMAIL_TEMPLATE)
            || !$this->_coreStoreConfig->getConfig(self::XML_PATH_CONFIRM_EMAIL_IDENTITY)
        ) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $this->_transportBuilder
            ->setTemplateIdentifier(
                $this->_coreStoreConfig->getConfig(self::XML_PATH_CONFIRM_EMAIL_TEMPLATE)
            )
            ->setTemplateOptions(
                array(
                    'area'  => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                )
            )
            ->setTemplateVars(
                array(
                    'subscriber' => $this,
                    'store'      => $this->_storeManager->getStore(),
                )
            )
            ->setFrom($this->_coreStoreConfig->getConfig(self::XML_PATH_CONFIRM_EMAIL_IDENTITY))
            ->addTo($this->getEmail(), $this->getName());
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Sends out confirmation success email
     *
     * @return $this
     */
    public function sendConfirmationSuccessEmail()
    {
        if (!$this->_coreStoreConfig->getConfig(self::XML_PATH_SUCCESS_EMAIL_TEMPLATE)
            || !$this->_coreStoreConfig->getConfig(self::XML_PATH_SUCCESS_EMAIL_IDENTITY)
        ) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $this->_transportBuilder
            ->setTemplateIdentifier(
                $this->_coreStoreConfig->getConfig(self::XML_PATH_SUCCESS_EMAIL_TEMPLATE)
            )
            ->setTemplateOptions(
                array(
                    'area'  => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                )
            )
            ->setTemplateVars(array('subscriber' => $this))
            ->setFrom($this->_coreStoreConfig->getConfig(self::XML_PATH_SUCCESS_EMAIL_IDENTITY))
            ->addTo($this->getEmail(), $this->getName());
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Sends out unsubscription email
     *
     * @return $this
     */
    public function sendUnsubscriptionEmail()
    {
        if (!$this->_coreStoreConfig->getConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE)
            || !$this->_coreStoreConfig->getConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY)
        ) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $this->_transportBuilder
            ->setTemplateIdentifier(
                $this->_coreStoreConfig->getConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE)
            )
            ->setTemplateOptions(
                array(
                    'area'  => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                )
            )
            ->setTemplateVars(array('subscriber' => $this))
            ->setFrom(
                $this->_coreStoreConfig->getConfig(self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY)
            )
            ->addTo($this->getEmail(), $this->getName());
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

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
