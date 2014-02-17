<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation data model
 *
 * @method \Magento\Invitation\Model\Resource\Invitation _getResource()
 * @method \Magento\Invitation\Model\Resource\Invitation getResource()
 * @method int getCustomerId()
 * @method \Magento\Invitation\Model\Invitation setCustomerId(int $value)
 * @method string getInvitationDate()
 * @method \Magento\Invitation\Model\Invitation setInvitationDate(string $value)
 * @method string getEmail()
 * @method \Magento\Invitation\Model\Invitation setEmail(string $value)
 * @method int getReferralId()
 * @method \Magento\Invitation\Model\Invitation setReferralId(int $value)
 * @method string getProtectionCode()
 * @method \Magento\Invitation\Model\Invitation setProtectionCode(string $value)
 * @method string getSignupDate()
 * @method \Magento\Invitation\Model\Invitation setSignupDate(string $value)
 * @method \Magento\Invitation\Model\Invitation setStoreId(int $value)
 * @method int getGroupId()
 * @method \Magento\Invitation\Model\Invitation setGroupId(int $value)
 * @method string getMessage()
 * @method \Magento\Invitation\Model\Invitation setMessage(string $value)
 * @method string getStatus()
 * @method \Magento\Invitation\Model\Invitation setStatus(string $value)
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Invitation\Model;

class Invitation extends \Magento\Core\Model\AbstractModel
{
    const STATUS_NEW = 'new';
    const STATUS_SENT = 'sent';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_CANCELED = 'canceled';

    const XML_PATH_EMAIL_IDENTITY = 'magento_invitation/email/identity';
    const XML_PATH_EMAIL_TEMPLATE = 'magento_invitation/email/template';

    const ERROR_STATUS = 1;
    const ERROR_INVALID_DATA = 2;
    const ERROR_CUSTOMER_EXISTS = 3;

    /**
     * @inheritdoc
     */
    private static $_customerExistsLookup = array();

    /**
     * @inheritdoc
     */
    protected $_eventPrefix = 'magento_invitation';

    /**
     * @inheritdoc
     */
    protected $_eventObject = 'invitation';

    /**
     * Invitation data
     *
     * @var \Magento\Invitation\Helper\Data
     */
    protected $_invitationData;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Invitation Config
     *
     * @var \Magento\Invitation\Model\Config
     */
    protected $_config;

    /**
     * Invitation History Factory
     *
     * @var \Magento\Invitation\Model\Invitation\HistoryFactory
     */
    protected $_historyFactory;

    /**
     * Customer Factory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Invitation\Helper\Data $invitationData
     * @param \Magento\Invitation\Model\Resource\Invitation $resource
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Invitation\Model\Config $config
     * @param \Magento\Invitation\Model\Invitation\HistoryFactory $historyFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Invitation\Helper\Data $invitationData,
        \Magento\Invitation\Model\Resource\Invitation $resource,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Invitation\Model\Config $config,
        \Magento\Invitation\Model\Invitation\HistoryFactory $historyFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Math\Random $mathRandom,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_invitationData = $invitationData;
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_historyFactory = $historyFactory;
        $this->_customerFactory = $customerFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->mathRandom = $mathRandom;
        $this->dateTime = $dateTime;
    }

    /**
     * Intialize resource
     */
    protected function _construct()
    {
        $this->_init('Magento\Invitation\Model\Resource\Invitation');
    }

    /**
     * Store ID getter
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->_getData('store_id');
        }
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Load invitation by an encrypted code
     *
     * @param string $code
     * @return $this
     * @throws \Magento\Core\Exception
     */
    public function loadByInvitationCode($code)
    {
        $code = explode(':', $code, 2);
        if (count($code) != 2) {
            throw new \Magento\Core\Exception(__('Please correct the invitation code.'));
        }
        list ($id, $protectionCode) = $code;
        $this->load($id);
        if (!$this->getId() || $this->getProtectionCode() != $protectionCode) {
            throw new \Magento\Core\Exception(__('Please correct the invitation code.'));
        }
        return $this;
    }

    /**
     * Model before save
     *
     * @throws \Magento\Core\Exception
     * @return $this
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            // set initial data for new one
            $this->addData(array(
                'protection_code' => $this->mathRandom->getUniqueHash(),
                'status'          => self::STATUS_NEW,
                'invitation_date' => $this->dateTime->formatDate(time()),
                'store_id'        => $this->getStoreId(),
            ));
            $inviter = $this->getInviter();
            if ($inviter) {
                $this->setCustomerId($inviter->getId());
            }
            if ($this->_config->getUseInviterGroup()) {
                if ($inviter) {
                    $this->setGroupId($inviter->getGroupId());
                }
                if (!$this->hasGroupId()) {
                    throw new \Magento\Core\Exception(__('You need to specify a customer ID group.'),
                        self::ERROR_INVALID_DATA);
                }
            } else {
                $this->unsetData('group_id');
            }

            if (!(int)$this->getStoreId()) {
                throw new \Magento\Core\Exception(__('The wrong store is specified.'), self::ERROR_INVALID_DATA);
            }
            $this->makeSureCustomerNotExists();
        } else {
            if ($this->dataHasChangedFor('message') && !$this->canMessageBeUpdated()) {
                throw new \Magento\Core\Exception(__("You can't update this message."), self::ERROR_STATUS);
            }
        }
        return parent::_beforeSave();
    }

    /**
     * Update status history after save
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $this->_historyFactory->create()
            ->setInvitationId($this->getId())->setStatus($this->getStatus())
            ->save();
        $parent = parent::_afterSave();
        if ($this->getStatus() === self::STATUS_NEW) {
            $this->setOrigData();
        }
        return $parent;
    }

    /**
     * Send invitation email
     *
     * @return bool
     */
    public function sendInvitationEmail()
    {
        $this->makeSureCanBeSent();
        $store = $this->_storeManager->getStore($this->getStoreId());

        $this->_transportBuilder
            ->setTemplateIdentifier($store->getConfig(self::XML_PATH_EMAIL_TEMPLATE))
            ->setTemplateOptions(array(
                'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                'store' => $this->getStoreId(),
            ))
            ->setTemplateVars(array(
                'url' => $this->_invitationData->getInvitationUrl($this),
                'message' => $this->getMessage(),
                'store' => $store,
                'store_name' => $store->getGroup()->getName(),
                'inviter_name' => ($this->getInviter() ? $this->getInviter()->getName() : null)
            ))
            ->setFrom($store->getConfig(self::XML_PATH_EMAIL_IDENTITY))
            ->addTo($this->getEmail());
        $transport = $this->_transportBuilder->getTransport();
        try {
            $transport->sendMessage();
        } catch (\Magento\Mail\Exception $e) {
            return false;
        }
        $this->setStatus(self::STATUS_SENT)->setUpdateDate(true)->save();
        return true;
    }

    /**
     * Get an encrypted invitation code
     *
     * @return string
     * @throws \Magento\Core\Exception
     */
    public function getInvitationCode()
    {
        if (!$this->getId()) {
            throw new \Magento\Core\Exception(__("We can't generate encrypted code."));
        }
        return $this->getId() . ':' . $this->getProtectionCode();
    }

    /**
     * Check and get customer if it was set
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getInviter()
    {
        $inviter = $this->getCustomer();
        if (!$inviter || !$inviter->getId()) {
            $inviter = null;
        }
        return $inviter;
    }

    /**
     * Check whether invitation can be sent
     *
     * @return void
     * @throws \Magento\Core\Exception
     */
    public function makeSureCanBeSent()
    {
        if (!$this->getId()) {
            throw new \Magento\Core\Exception(__("We couldn't find an ID for this invitation."),
                self::ERROR_INVALID_DATA);
        }
        if ($this->getStatus() !== self::STATUS_NEW) {
            throw new \Magento\Core\Exception(
                __('We cannot send an invitation with status "%1".', $this->getStatus()), self::ERROR_STATUS
            );
        }
        if (!$this->getEmail() || !\Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            throw new \Magento\Core\Exception(__('Please correct the invalid or empty invitation email.'),
                self::ERROR_INVALID_DATA);
        }
        $this->makeSureCustomerNotExists();
    }

    /**
     * Check whether customer with specified email exists
     *
     * @param string $email
     * @param string $websiteId
     * @return void
     * @throws \Magento\Core\Exception
     */
    public function makeSureCustomerNotExists($email = null, $websiteId = null)
    {
        if (null === $websiteId) {
            $websiteId = $this->_storeManager->getStore($this->getStoreId())->getWebsiteId();
        }
        if (!$websiteId) {
            throw new \Magento\Core\Exception(__("We can't identify the proper website."), self::ERROR_INVALID_DATA);
        }
        if (null === $email) {
            $email = $this->getEmail();
        }
        if (!$email) {
            throw new \Magento\Core\Exception(__('Please specify an email.'), self::ERROR_INVALID_DATA);
        }

        // lookup customer by specified email/website id
        if (!isset(self::$_customerExistsLookup[$email]) || !isset(self::$_customerExistsLookup[$email][$websiteId])) {
            $customer = $this->_customerFactory->create()
                ->setWebsiteId($websiteId)->loadByEmail($email);
            self::$_customerExistsLookup[$email][$websiteId] = ($customer->getId() ? $customer->getId() : false);
        }
        if (false === self::$_customerExistsLookup[$email][$websiteId]) {
            return;
        }
        throw new \Magento\Core\Exception(
            __('This invitation is addressed to a current customer: "%1".', $email), self::ERROR_CUSTOMER_EXISTS
        );
    }

    /**
     * Check whether this invitation can be accepted
     *
     * @param int|string $websiteId
     * @return void
     * @throws \Magento\Core\Exception
     */
    public function makeSureCanBeAccepted($websiteId = null)
    {
        $messageInvalid = __('This invitation is not valid.');
        if (!$this->getId()) {
            throw new \Magento\Core\Exception($messageInvalid, self::ERROR_STATUS);
        }
        if (!in_array($this->getStatus(), array(self::STATUS_NEW, self::STATUS_SENT))) {
            throw new \Magento\Core\Exception($messageInvalid, self::ERROR_STATUS);
        }
        if (null === $websiteId) {
            $websiteId = $this->_storeManager->getWebsite()->getId();
        }
        if ($websiteId != $this->_storeManager->getStore($this->getStoreId())->getWebsiteId()) {
            throw new \Magento\Core\Exception($messageInvalid, self::ERROR_STATUS);
        }
    }

    /**
     * Check whether message can be updated
     *
     * @return bool
     */
    public function canMessageBeUpdated()
    {
        return (bool)(int)$this->getId() && $this->getStatus() === self::STATUS_NEW;
    }

    /**
     * Check whether invitation can be cancelled
     *
     * @return bool
     */
    public function canBeCanceled()
    {
        return (bool)(int)$this->getId()
            && !in_array($this->getStatus(), array(self::STATUS_CANCELED, self::STATUS_ACCEPTED));
    }

    /**
     * Check whether invitation can be sent. Will throw exception on invalid data.
     *
     * @return bool
     * @throws \Magento\Core\Exception
     */
    public function canBeSent()
    {
        try {
            $this->makeSureCanBeSent();
            return true;
        } catch (\Magento\Core\Exception $e) {
            if ($e->getCode() && $e->getCode() === self::ERROR_INVALID_DATA) {
                throw $e;
            }
        }
        return false;
    }

    /**
     * Cancel the invitation
     *
     * @return $this
     */
    public function cancel()
    {
        if ($this->canBeCanceled()) {
            $this->setStatus(self::STATUS_CANCELED)->save();
        }
        return $this;
    }

    /**
     * Accept the invitation
     *
     * @param int|string $websiteId
     * @param int $referralId
     * @return $this
     */
    public function accept($websiteId, $referralId)
    {
        $this->makeSureCanBeAccepted($websiteId);
        $this->setReferralId($referralId)
            ->setStatus(self::STATUS_ACCEPTED)
            ->setSignupDate($this->dateTime->formatDate(time()))
            ->save();
        $inviterId = $this->getCustomerId();
        if ($inviterId) {
            $this->getResource()->trackReferral($inviterId, $referralId);
        }
        return $this;
    }

    /**
     * Check whether invitation can be accepted
     *
     * @param int $websiteId
     * @return bool
     */
    public function canBeAccepted($websiteId = null)
    {
        try {
            $this->makeSureCanBeAccepted($websiteId);
            return true;
        } catch (\Magento\Core\Exception $e) {
            // intentionally jammed
        }
        return false;
    }

    /**
     * Validating invitation's parameters
     *
     * Returns true or array of errors
     *
     * @return string[]|bool
     */
    public function validate()
    {
        $errors = array();

        if (!\Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = __('Please correct the invitation email.');
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

}
