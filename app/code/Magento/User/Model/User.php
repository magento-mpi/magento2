<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin user model
 *
 * @method Magento_User_Model_Resource_User _getResource()
 * @method Magento_User_Model_Resource_User getResource()
 * @method string getFirstname()
 * @method Magento_User_Model_User setFirstname(string $value)
 * @method string getLastname()
 * @method Magento_User_Model_User setLastname(string $value)
 * @method string getEmail()
 * @method Magento_User_Model_User setEmail(string $value)
 * @method string getUsername()
 * @method Magento_User_Model_User setUsername(string $value)
 * @method string getPassword()
 * @method Magento_User_Model_User setPassword(string $value)
 * @method string getCreated()
 * @method Magento_User_Model_User setCreated(string $value)
 * @method string getModified()
 * @method Magento_User_Model_User setModified(string $value)
 * @method string getLogdate()
 * @method Magento_User_Model_User setLogdate(string $value)
 * @method int getLognum()
 * @method Magento_User_Model_User setLognum(int $value)
 * @method int getReloadAclFlag()
 * @method Magento_User_Model_User setReloadAclFlag(int $value)
 * @method int getIsActive()
 * @method Magento_User_Model_User setIsActive(int $value)
 * @method string getExtra()
 * @method Magento_User_Model_User setExtra(string $value)
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_User_Model_User
    extends Magento_Core_Model_Abstract
    implements Magento_Backend_Model_Auth_Credential_StorageInterface
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
     * @var Magento_User_Model_Role
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
     * @var  Magento_Core_Model_Email_Template_Mailer
     */
    protected $_mailer;

    /** @var Magento_Core_Model_Sender */
    protected $_sender;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * User data
     *
     * @var Magento_User_Helper_Data
     */
    protected $_userData = null;

    /**
     * @param Magento_User_Helper_Data $userData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Sender $sender
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_User_Helper_Data $userData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Sender $sender,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_userData = $userData;
        $this->_coreData = $coreData;
        $this->_sender = $sender;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize user model
     */
    protected function _construct()
    {
        $this->_init('Magento_User_Model_Resource_User');
    }

    /**
     * Processing data before model save
     *
     * @return Magento_User_Model_User
     */
    protected function _beforeSave()
    {
        $data = array(
            'firstname' => $this->getFirstname(),
            'lastname'  => $this->getLastname(),
            'email'     => $this->getEmail(),
            'modified'  => now(),
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
     * @return Zend_Validate_Interface
     */
    protected function _getValidationRulesBeforeSave()
    {
        $userNameNotEmpty = new Zend_Validate_NotEmpty();
        $userNameNotEmpty->setMessage(
            __('User Name is a required field.'),
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $firstNameNotEmpty = new Zend_Validate_NotEmpty();
        $firstNameNotEmpty->setMessage(
            __('First Name is a required field.'),
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $lastNameNotEmpty = new Zend_Validate_NotEmpty();
        $lastNameNotEmpty->setMessage(
            __('Last Name is a required field.'),
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $emailValidity = new Zend_Validate_EmailAddress();
        $emailValidity->setMessage(
            __('Please enter a valid email.'),
            Zend_Validate_EmailAddress::INVALID
        );

        /** @var $validator Magento_Validator_Composite_VarienObject */
        $validator = Mage::getModel('Magento_Validator_Composite_VarienObject');
        $validator
            ->addRule($userNameNotEmpty, 'username')
            ->addRule($firstNameNotEmpty, 'firstname')
            ->addRule($lastNameNotEmpty, 'lastname')
            ->addRule($emailValidity, 'email')
        ;

        if ($this->_willSavePassword()) {
            $this->_addPasswordValidation($validator);
        }
        return $validator;
    }

    /**
     * Add validation rules for the password management fields
     *
     * @param Magento_Validator_Composite_VarienObject $validator
     */
    protected function _addPasswordValidation(Magento_Validator_Composite_VarienObject $validator)
    {
        $passwordNotEmpty = new Zend_Validate_NotEmpty();
        $passwordNotEmpty->setMessage(
            __('Password is required field.'),
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $minPassLength = self::MIN_PASSWORD_LENGTH;
        $passwordLength = new Zend_Validate_StringLength(array('min' => $minPassLength, 'encoding' => 'UTF-8'));
        $passwordLength->setMessage(
            __('Your password must be at least %1 characters.', $minPassLength),
            Zend_Validate_StringLength::TOO_SHORT
        );
        $passwordChars = new Zend_Validate_Regex('/[a-z].*\d|\d.*[a-z]/iu');
        $passwordChars->setMessage(
            __('Your password must include both numeric and alphabetic characters.'),
            Zend_Validate_Regex::NOT_MATCH
        );
        $validator
            ->addRule($passwordNotEmpty, 'password')
            ->addRule($passwordLength, 'password')
            ->addRule($passwordChars, 'password')
        ;
        if ($this->hasPasswordConfirmation()) {
            $passwordConfirmation = new Zend_Validate_Identical($this->getPasswordConfirmation());
            $passwordConfirmation->setMessage(
                __('Your password confirmation must match your password.'),
                Zend_Validate_Identical::NOT_SAME
            );
            $validator->addRule($passwordConfirmation, 'password');
        }
    }

    /**
     * Process data after model is saved
     *
     * @return Magento_Core_Model_Abstract
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
     * @return  Magento_User_Model_User
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
     * @return Magento_User_Model_Role
     */
    public function getRole()
    {
        if (null === $this->_role) {
            $this->_role = Mage::getModel('Magento_User_Model_Role');
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
     * @return Magento_User_Model_User
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
     * Retrieve admin user collection
     *
     * @return Magento_User_Model_Resource_User_Collection
     */
    public function getCollection()
    {
        return Mage::getResourceModel('Magento_User_Model_Resource_User_Collection');
    }

    /**
     * Set custom mail handler
     *
     * @param Magento_Core_Model_Email_Template_Mailer $mailer
     * @return Magento_User_Model_User
     */
    public function setMailer(Magento_Core_Model_Email_Template_Mailer $mailer)
    {
        $this->_mailer = $mailer;
        return $this;
    }

    /**
     * Retrieve mailer
     *
     * @return Magento_Core_Model_Email_Template_Mailer
     */
    protected function _getMailer()
    {
        if (!$this->_mailer) {
            $this->_mailer = Mage::getModel('Magento_Core_Model_Email_Template_Mailer');
        }
        return $this->_mailer;
    }

    /**
     * Send email with reset password confirmation link
     *
     * @return Magento_User_Model_User
     */
    public function sendPasswordResetConfirmationEmail()
    {
        $mailer = $this->_getMailer();
        /** @var $mailer Magento_Core_Model_Email_Template_Mailer */
        $emailInfo = Mage::getModel('Magento_Core_Model_Email_Info');
        $emailInfo->addTo($this->getEmail(), $this->getName());
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_IDENTITY));
        $mailer->setStoreId(0);
        $mailer->setTemplateId(Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_TEMPLATE));
        $mailer->setTemplateParams(array(
            'user' => $this
        ));
        $mailer->send();

        return $this;
    }

    /**
     * Send email to when password is resetting
     *
     * @return Magento_User_Model_User
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
        return 'U' . $this->getUserId();
    }

    /**
     * Authenticate user name and password and save loaded record
     *
     * @param string $username
     * @param string $password
     * @return boolean
     * @throws Magento_Core_Exception
     * @throws Magento_Backend_Model_Auth_Exception
     * @throws Magento_Backend_Model_Auth_Plugin_Exception
     */
    public function authenticate($username, $password)
    {
        $config = Mage::getStoreConfigFlag('admin/security/use_case_sensitive_login');
        $result = false;

        try {
            Mage::dispatchEvent('admin_user_authenticate_before', array(
                'username' => $username,
                'user'     => $this
            ));
            $this->loadByUsername($username);
            $sensitive = ($config) ? $username == $this->getUsername() : true;

            if ($sensitive
                && $this->getId()
                && $this->_coreData->validateHash($password, $this->getPassword())
            ) {
                if ($this->getIsActive() != '1') {
                    throw new Magento_Backend_Model_Auth_Exception(
                        __('This account is inactive.')
                    );
                }
                if (!$this->hasAssigned2Role($this->getId())) {
                    throw new Magento_Backend_Model_Auth_Exception(
                        __('Access denied.')
                    );
                }
                $result = true;
            }

            Mage::dispatchEvent('admin_user_authenticate_after', array(
                'username' => $username,
                'password' => $password,
                'user'     => $this,
                'result'   => $result,
            ));
        } catch (Magento_Core_Exception $e) {
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
     * @return  Magento_User_Model_User
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
     * @return Magento_User_Model_User
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
     * @return Magento_User_Model_User
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
     * @param int|Magento_User_Model_User $user
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
        return $this->_coreData->getHash($password, 2);
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token and its creation time
     *
     * @param string $newToken
     * @return Magento_User_Model_User
     * @throws Magento_Core_Exception
     */
    public function changeResetPasswordLinkToken($newToken)
    {
        if (!is_string($newToken) || empty($newToken)) {
            Mage::throwException(
                'Magento_Core',
                __('Please correct the password reset token.')
            );
        }
        $this->setRpToken($newToken);
        $currentDate = Magento_Date::now();
        $this->setRpTokenCreatedAt($currentDate);

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

        $currentDate = Magento_Date::now();
        $currentTimestamp = Magento_Date::toTimestamp($currentDate);
        $tokenTimestamp = Magento_Date::toTimestamp($linkTokenCreatedAt);
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
     * @return Magento_User_Model_User
     */
    public function setHasAvailableResources($hasResources)
    {
        $this->_hasResources = $hasResources;
        return $this;
    }
}
