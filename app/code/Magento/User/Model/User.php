<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Model;

/**
 * Admin user model
 *
 * @method \Magento\User\Model\Resource\User _getResource()
 * @method \Magento\User\Model\Resource\User getResource()
 * @method string getFirstname()
 * @method \Magento\User\Model\User setFirstname(string $value)
 * @method string getLastname()
 * @method \Magento\User\Model\User setLastname(string $value)
 * @method string getEmail()
 * @method \Magento\User\Model\User setEmail(string $value)
 * @method string getUsername()
 * @method \Magento\User\Model\User setUsername(string $value)
 * @method string getPassword()
 * @method \Magento\User\Model\User setPassword(string $value)
 * @method string getCreated()
 * @method \Magento\User\Model\User setCreated(string $value)
 * @method string getModified()
 * @method \Magento\User\Model\User setModified(string $value)
 * @method string getLogdate()
 * @method \Magento\User\Model\User setLogdate(string $value)
 * @method int getLognum()
 * @method \Magento\User\Model\User setLognum(int $value)
 * @method int getReloadAclFlag()
 * @method \Magento\User\Model\User setReloadAclFlag(int $value)
 * @method int getIsActive()
 * @method \Magento\User\Model\User setIsActive(int $value)
 * @method string getExtra()
 * @method \Magento\User\Model\User setExtra(string $value)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class User
    extends \Magento\Core\Model\AbstractModel
    implements \Magento\Backend\Model\Auth\Credential\StorageInterface
{
    /**
     * Configuration paths for email templates and identities
     */
    const XML_PATH_FORGOT_EMAIL_TEMPLATE    = 'admin/emails/forgot_email_template';
    const XML_PATH_FORGOT_EMAIL_IDENTITY    = 'admin/emails/forgot_email_identity';

    const XML_PATH_RESET_PASSWORD_TEMPLATE  = 'admin/emails/reset_password_template';

    /**
     * Minimum length of admin password
     */
    const MIN_PASSWORD_LENGTH = 7;

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'admin_user';

    /**
     * Admin role
     *
     * @var \Magento\User\Model\Role
     */
    protected $_role;

    /**
     * Available resources flag
     *
     * @var boolean
     */
    protected $_hasResources = true;

    /**
     * Mail handler
     *
     * @var  \Magento\Email\Model\Template\Mailer
     */
    protected $_mailer;

    /** @var \Magento\Email\Model\Sender */
    protected $_sender;

    /**
     * User data
     *
     * @var \Magento\User\Helper\Data
     */
    protected $_userData = null;

    /**
     * Core store config
     *
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_config;

    /**
     * Factory for validator composite object
     *
     * @var \Magento\Validator\Composite\VarienObjectFactory
     */
    protected $_validatorComposite;

    /**
     * Role model factory
     *
     * @var \Magento\User\Model\RoleFactory
     */
    protected $_roleFactory;

    /**
     * Factory for email info model
     *
     * @var \Magento\Email\Model\InfoFactory
     */
    protected $_emailInfoFactory;

    /**
     * @var \Magento\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\User\Helper\Data $userData
     * @param \Magento\Email\Model\Sender $sender
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Magento\Validator\Composite\VarienObjectFactory $validatorCompositeFactory
     * @param \Magento\User\Model\RoleFactory $roleFactory
     * @param \Magento\Email\Model\InfoFactory $emailInfoFactory
     * @param \Magento\Email\Model\Template\MailerFactory $mailerFactory
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\User\Helper\Data $userData,
        \Magento\Email\Model\Sender $sender,
        \Magento\Backend\App\ConfigInterface $config,
        \Magento\Validator\Composite\VarienObjectFactory $validatorCompositeFactory,
        \Magento\User\Model\RoleFactory $roleFactory,
        \Magento\Email\Model\InfoFactory $emailInfoFactory,
        \Magento\Email\Model\Template\MailerFactory $mailerFactory,
        \Magento\Encryption\EncryptorInterface $encryptor,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_encryptor = $encryptor;
        $this->dateTime = $dateTime;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_userData = $userData;
        $this->_sender = $sender;
        $this->_config = $config;
        $this->_validatorComposite = $validatorCompositeFactory;
        $this->_roleFactory = $roleFactory;
        $this->_emailInfoFactory = $emailInfoFactory;
        $this->_mailer = $mailerFactory->create();
    }

    /**
     * Initialize user model
     */
    protected function _construct()
    {
        $this->_init('Magento\User\Model\Resource\User');
    }

    public function __sleep()
    {
        $properties = parent::__sleep();
        return array_diff($properties, array(
            '_eventManager',
            '_sender',
            '_userData',
            '_config',
            '_validatorComposite',
            '_roleFactory',
            '_emailInfoFactory',
            '_mailer',
            '_encryptor'
        ));
    }

    public function __wakeup()
    {
        parent::__wakeup();
        $objectManager = \Magento\App\ObjectManager::getInstance();
        $this->_eventManager    = $objectManager->get('Magento\Event\ManagerInterface');
        $this->_sender          = $objectManager->get('Magento\Email\Model\Sender');
        $this->_userData        = $objectManager->get('Magento\User\Helper\Data');
        $this->_config = $objectManager->get('Magento\Backend\App\ConfigInterface');
        $this->_coreRegistry    = $objectManager->get('Magento\Core\Model\Registry');
        $this->_validatorComposite = $objectManager->get('Magento\Validator\Composite\VarienObjectFactory');
        $this->_roleFactory = $objectManager->get('Magento\User\Model\RoleFactory');
        $this->_emailInfoFactory = $objectManager->get('Magento\Email\Model\InfoFactory');
        $this->_mailer = $objectManager->get('Magento\Email\Model\Template\MailerFactory');
        $this->_encryptor = $objectManager->get('Magento\Encryption\EncryptorInterface');
    }

    /**
     * Processing data before model save
     *
     * @return \Magento\User\Model\User
     */
    protected function _beforeSave()
    {
        $data = array(
            'firstname' => $this->getFirstname(),
            'lastname'  => $this->getLastname(),
            'email'     => $this->getEmail(),
            'modified'  => $this->dateTime->now(),
            'extra'     => serialize($this->getExtra())
        );

        if ($this->getId() > 0) {
            $data['user_id'] = $this->getId();
        }

        if ( $this->getUsername() ) {
            $data['username'] = $this->getUsername();
        }

        if ($this->_willSavePassword()) {
            $data['password'] = $this->_getEncodedPassword($this->getPassword());
        }

        if (!is_null($this->getIsActive())) {
            $data['is_active'] = intval($this->getIsActive());
        }

        $this->addData($data);

        return parent::_beforeSave();
    }

    /**
     * Whether the password saving is going to occur
     *
     * @return bool
     */
    protected function _willSavePassword()
    {
        return ($this->isObjectNew() || ($this->hasData('password') && $this->dataHasChangedFor('password')));
    }

    /**
     * Add validation rules for particular fields
     *
     * @return \Zend_Validate_Interface
     */
    protected function _getValidationRulesBeforeSave()
    {
        $userNameNotEmpty = new \Zend_Validate_NotEmpty();
        $userNameNotEmpty->setMessage(
            __('User Name is a required field.'),
            \Zend_Validate_NotEmpty::IS_EMPTY
        );
        $firstNameNotEmpty = new \Zend_Validate_NotEmpty();
        $firstNameNotEmpty->setMessage(
            __('First Name is a required field.'),
            \Zend_Validate_NotEmpty::IS_EMPTY
        );
        $lastNameNotEmpty = new \Zend_Validate_NotEmpty();
        $lastNameNotEmpty->setMessage(
            __('Last Name is a required field.'),
            \Zend_Validate_NotEmpty::IS_EMPTY
        );
        $emailValidity = new \Zend_Validate_EmailAddress();
        $emailValidity->setMessage(
            __('Please enter a valid email.'),
            \Zend_Validate_EmailAddress::INVALID
        );

        /** @var $validator \Magento\Validator\Composite\VarienObject */
        $validator = $this->_validatorComposite->create();
        $validator->addRule($userNameNotEmpty, 'username')
            ->addRule($firstNameNotEmpty, 'firstname')
            ->addRule($lastNameNotEmpty, 'lastname')
            ->addRule($emailValidity, 'email');

        if ($this->_willSavePassword()) {
            $this->_addPasswordValidation($validator);
        }
        return $validator;
    }

    /**
     * Add validation rules for the password management fields
     *
     * @param \Magento\Validator\Composite\VarienObject $validator
     */
    protected function _addPasswordValidation(\Magento\Validator\Composite\VarienObject $validator)
    {
        $passwordNotEmpty = new \Zend_Validate_NotEmpty();
        $passwordNotEmpty->setMessage(
            __('Password is required field.'),
            \Zend_Validate_NotEmpty::IS_EMPTY
        );
        $minPassLength = self::MIN_PASSWORD_LENGTH;
        $passwordLength = new \Zend_Validate_StringLength(array('min' => $minPassLength, 'encoding' => 'UTF-8'));
        $passwordLength->setMessage(
            __('Your password must be at least %1 characters.', $minPassLength),
            \Zend_Validate_StringLength::TOO_SHORT
        );
        $passwordChars = new \Zend_Validate_Regex('/[a-z].*\d|\d.*[a-z]/iu');
        $passwordChars->setMessage(
            __('Your password must include both numeric and alphabetic characters.'),
            \Zend_Validate_Regex::NOT_MATCH
        );
        $validator
            ->addRule($passwordNotEmpty, 'password')
            ->addRule($passwordLength, 'password')
            ->addRule($passwordChars, 'password')
        ;
        if ($this->hasPasswordConfirmation()) {
            $passwordConfirmation = new \Zend_Validate_Identical($this->getPasswordConfirmation());
            $passwordConfirmation->setMessage(
                __('Your password confirmation must match your password.'),
                \Zend_Validate_Identical::NOT_SAME
            );
            $validator->addRule($passwordConfirmation, 'password');
        }
    }

    /**
     * Process data after model is saved
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _afterSave()
    {
        $this->_role = null;
        return parent::_afterSave();
    }

    /**
     * Save admin user extra data (like configuration sections state)
     *
     * @param   array $data
     * @return  \Magento\User\Model\User
     */
    public function saveExtra($data)
    {
        if (is_array($data)) {
            $data = serialize($data);
        }
        $this->_getResource()->saveExtra($this, $data);
        return $this;
    }

    /**
     * Retrieve user roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->_getResource()->getRoles($this);
    }

    /**
     * Get admin role model
     *
     * @return \Magento\User\Model\Role
     */
    public function getRole()
    {
        if (null === $this->_role) {
            $this->_role = $this->_roleFactory->create();
            $roles = $this->getRoles();
            if ($roles && isset($roles[0]) && $roles[0]) {
                $this->_role->load($roles[0]);
            }
        }
        return $this->_role;
    }

    /**
     * Unassign user from his current role
     *
     * @return \Magento\User\Model\User
     */
    public function deleteFromRole()
    {
        $this->_getResource()->deleteFromRole($this);
        return $this;
    }

    /**
     * Check if such combination role/user exists
     *
     * @return boolean
     */
    public function roleUserExists()
    {
        $result = $this->_getResource()->roleUserExists($this);
        return (is_array($result) && count($result) > 0) ? true : false;
    }

    /**
     * Set custom mail handler
     *
     * @param \Magento\Email\Model\Template\Mailer $mailer
     * @return \Magento\User\Model\User
     */
    public function setMailer(\Magento\Email\Model\Template\Mailer $mailer)
    {
        $this->_mailer = $mailer;
        return $this;
    }

    /**
     * Send email with reset password confirmation link
     *
     * @return \Magento\User\Model\User
     */
    public function sendPasswordResetConfirmationEmail()
    {
        /** @var \Magento\Email\Model\Info $emailInfo */
        $emailInfo = $this->_emailInfoFactory->create();
        $emailInfo->addTo($this->getEmail(), $this->getName());
        $this->_mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $this->_mailer->setSender($this->_config->getValue(self::XML_PATH_FORGOT_EMAIL_IDENTITY));
        $this->_mailer->setStoreId(0);
        $this->_mailer->setTemplateId($this->_config->getValue(self::XML_PATH_FORGOT_EMAIL_TEMPLATE));
        $this->_mailer->setTemplateParams(array(
            'user' => $this
        ));
        $this->_mailer->send();

        return $this;
    }

    /**
     * Send email to when password is resetting
     *
     * @return \Magento\User\Model\User
     */
    public function sendPasswordResetNotificationEmail()
    {
        $this->_sender->send(
            $this->getEmail(),
            $this->getName(),
            self::XML_PATH_RESET_PASSWORD_TEMPLATE,
            self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('user' => $this),
            0
        );
        return $this;
    }

    /**
     * Retrieve user name
     *
     * @param string $separator
     * @return string
     */
    public function getName($separator = ' ')
    {
        return $this->getFirstname() . $separator . $this->getLastname();
    }

    /**
     * Retrieve user identifier
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getUserId();
    }

    /**
     * Get user ACL role
     *
     * @return string
     */
    public function getAclRole()
    {
        return $this->getRole()->getId();
    }

    /**
     * Authenticate user name and password and save loaded record
     *
     * @param string $username
     * @param string $password
     * @return boolean
     * @throws \Magento\Core\Exception
     * @throws \Magento\Backend\Model\Auth\Exception
     * @throws \Magento\Backend\Model\Auth\Plugin\Exception
     */
    public function authenticate($username, $password)
    {
        $config = $this->_config->isSetFlag('admin/security/use_case_sensitive_login');
        $result = false;

        try {
            $this->_eventManager->dispatch('admin_user_authenticate_before', array(
                'username' => $username,
                'user'     => $this
            ));
            $this->loadByUsername($username);
            $sensitive = ($config) ? $username == $this->getUsername() : true;

            if ($sensitive
                && $this->getId()
                && $this->_encryptor->validateHash($password, $this->getPassword())
            ) {
                if ($this->getIsActive() != '1') {
                    throw new \Magento\Backend\Model\Auth\Exception(
                        __('This account is inactive.')
                    );
                }
                if (!$this->hasAssigned2Role($this->getId())) {
                    throw new \Magento\Backend\Model\Auth\Exception(
                        __('Access denied.')
                    );
                }
                $result = true;
            }

            $this->_eventManager->dispatch('admin_user_authenticate_after', array(
                'username' => $username,
                'password' => $password,
                'user'     => $this,
                'result'   => $result,
            ));
        } catch (\Magento\Core\Exception $e) {
            $this->unsetData();
            throw $e;
        }

        if (!$result) {
            $this->unsetData();
        }
        return $result;
    }

    /**
     * Login user
     *
     * @param   string $username
     * @param   string $password
     * @return  \Magento\User\Model\User
     */
    public function login($username, $password)
    {
        if ($this->authenticate($username, $password)) {
            $this->getResource()->recordLogin($this);
        }
        return $this;
    }

    /**
     * Reload current user
     *
     * @return \Magento\User\Model\User
     */
    public function reload()
    {
        $userId = $this->getId();
        $this->setId(null);
        $this->load($userId);
        return $this;
    }

    /**
     * Load user by its username
     *
     * @param string $username
     * @return \Magento\User\Model\User
     */
    public function loadByUsername($username)
    {
        $data = $this->getResource()->loadByUsername($username);
        if ($data !== false) {
            $this->setData($data);
        }
        return $this;
    }

    /**
     * Check if user is assigned to any role
     *
     * @param int|\Magento\User\Model\User $user
     * @return null|boolean|array
     */
    public function hasAssigned2Role($user)
    {
        return $this->getResource()->hasAssigned2Role($user);
    }

    /**
     * Retrieve encoded password
     *
     * @param string $password
     * @return string
     */
    protected function _getEncodedPassword($password)
    {
        return $this->_encryptor->getHash($password, 2);
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token and its creation time
     *
     * @param string $newToken
     * @return \Magento\User\Model\User
     * @throws \Magento\Core\Exception
     */
    public function changeResetPasswordLinkToken($newToken)
    {
        if (!is_string($newToken) || empty($newToken)) {
            throw new \Magento\Core\Exception(__('Please correct the password reset token.'));
        }
        $this->setRpToken($newToken);
        $this->setRpTokenCreatedAt($this->dateTime->now());

        return $this;
    }

    /**
     * Check if current reset password link token is expired
     *
     * @return boolean
     */
    public function isResetPasswordLinkTokenExpired()
    {
        $linkToken = $this->getRpToken();
        $linkTokenCreatedAt = $this->getRpTokenCreatedAt();

        if (empty($linkToken) || empty($linkTokenCreatedAt)) {
            return true;
        }

        $expirationPeriod = $this->_userData->getResetPasswordLinkExpirationPeriod();

        $currentTimestamp = $this->dateTime->toTimestamp($this->dateTime->now());
        $tokenTimestamp = $this->dateTime->toTimestamp($linkTokenCreatedAt);
        if ($tokenTimestamp > $currentTimestamp) {
            return true;
        }

        $dayDifference = floor(($currentTimestamp - $tokenTimestamp) / (24 * 60 * 60));
        if ($dayDifference >= $expirationPeriod) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has available resources
     *
     * @return bool
     */
    public function hasAvailableResources()
    {
        return $this->_hasResources;
    }

    /**
     * Set user has available resources
     *
     * @param bool $hasResources
     * @return \Magento\User\Model\User
     */
    public function setHasAvailableResources($hasResources)
    {
        $this->_hasResources = $hasResources;
        return $this;
    }
}
