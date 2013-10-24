<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Integration model
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method \Magento\Integration\Model\Resource\Integration _getResource()
 * @method \Magento\Integration\Model\Resource\Integration getResource()
 * @method \Magento\Integration\Model\Resource\Integration\Collection getCollection()
 * @method \Magento\Integration\Model\Resource\Integration\Collection getResourceCollection()
 * @method string getName()
 * @method \Magento\Integration\Model\Integration setName(string $name)
 * @method string getEmail()
 * @method \Magento\Integration\Model\Integration setEmail(string $email)
 * @method int getStatus()
 * @method \Magento\Integration\Model\Integration getStatus(int $value)
 * @method int getAuthentication()
 * @method \Magento\Integration\Model\Integration setAuthentication(int $value)
 * @method string getEndpoint()
 * @method \Magento\Integration\Model\Integration setEndpoint(string $endpoint)
 */
namespace Magento\Integration\Model;

class Integration extends \Magento\Core\Model\AbstractModel
{
    /**#@+
     * Integration statuses.
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /**#@-*/

    /**#@+
     * Authentication mechanism
     */
    const AUTHENTICATION_OAUTH = 1;
    const AUTHENTICATION_MANUAL = 2;
    /**#@-*/

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'integration';

    /**
     * @var \Magento\Core\Model\Url\Validator
     */
    protected $_urlValidator;

    /**
     * @param \Magento\Core\Model\Url\Validator $urlValidator
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    /*
    public function __construct(
        \Magento\Core\Model\Url\Validator $urlValidator,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_urlValidator = $urlValidator;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    */
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Integration\Model\Resource\Integration');
    }

    /**
     * BeforeSave actions
     *
     * @return \Magento\Integration\Model\Integration
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setUpdatedAt(time());
        }
        $this->validate();
        parent::_beforeSave();
        return $this;
    }

    /**
     * Validate data
     *
     * @return array|bool
     * @throw \Magento\Core\Exception|Exception   Throw exception on fail validation
     */
    public function validate()
    {
        /*
        if ($this->getCallbackUrl() || $this->getRejectedCallbackUrl()) {
            $this->setCallbackUrl(trim($this->getCallbackUrl()));
            $this->setRejectedCallbackUrl(trim($this->getRejectedCallbackUrl()));

            if ($this->getCallbackUrl() && !$this->_urlValidator->isValid($this->getCallbackUrl())) {
                throw new \Magento\Core\Exception(__('Invalid Callback URL'));
            }
            if ($this->getRejectedCallbackUrl() && !$this->_urlValidator->isValid($this->getRejectedCallbackUrl())) {
                throw new \Magento\Core\Exception(__('Invalid Rejected Callback URL'));
            }
        }

        /-** @var $validatorLength \Magento\Oauth\Model\Consumer\Validator\KeyLength *-/
        $validatorLength = $this->keyLengthFactory->create(
            array('options' => array('length' => self::KEY_LENGTH))
        );

        $validatorLength->setName('Consumer Key');
        if (!$validatorLength->isValid($this->getKey())) {
            $messages = $validatorLength->getMessages();
            throw new \Magento\Core\Exception(array_shift($messages));
        }

        $validatorLength->setLength(self::SECRET_LENGTH);
        $validatorLength->setName('Consumer Secret');
        if (!$validatorLength->isValid($this->getSecret())) {
            $messages = $validatorLength->getMessages();
            throw new \Magento\Core\Exception(array_shift($messages));
        }
        */
        return true;
    }

}
