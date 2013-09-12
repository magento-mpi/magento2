<?php
/**
 * Represents an endpoint to which messages can be sent
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method string getName()
 * @method Magento_Webhook_Model_Endpoint setName(string $value)
 * @method Magento_Webhook_Model_Endpoint setEndpointUrl(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Webhook_Model_Endpoint setUpdatedAt(string $value)
 * @method Magento_Webhook_Model_Endpoint setFormat(string $value)
 * @method string getApiUserId()
 * @method Magento_Webhook_Model_Endpoint setApiUserId(string $value)
 * @method Magento_Webhook_Model_Endpoint setAuthenticationType(string $value)
 * @method Magento_Webhook_Model_Endpoint setTimeoutInSecs(string $value)
 */
class Magento_Webhook_Model_Endpoint extends Magento_Core_Model_Abstract implements Magento_Outbound_EndpointInterface
{
    /**
     * Used to create a User abstraction from a given webapi user associated with this subscription.
     * @var Magento_Webhook_Model_User_Factory
     */
    private $_userFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Webhook_Model_User_Factory $userFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Webhook_Model_User_Factory $userFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_userFactory = $userFactory;
    }

    /**
     * Initialize model
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Webhook_Model_Resource_Endpoint');
    }

    /**
     * Return subscription endpoint url for compatibility with interface
     *
     * @return string
     */
    public function getEndpointUrl()
    {
        return $this->getData('endpoint_url');
    }

    /**
     * Return subscription timeout in secs for compatibility with interface
     *
     * @return string
     */
    public function getTimeoutInSecs()
    {
        return $this->getData('timeout_in_secs');
    }

    /**
     * Prepare data to be saved to database
     *
     * @return Magento_Core_Model_Abstract
     * @throws Magento_Webhook_Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->hasAuthenticationType()) {
            $this->setAuthenticationType(Magento_Outbound_EndpointInterface::AUTH_TYPE_NONE);
        }

        if ($this->hasDataChanges()) {
            $this->setUpdatedAt($this->_getResource()->formatDate(time()));
        }

        return $this;
    }

    /**
     * Returns the format this message should be sent in (JSON, XML, etc.)
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->getData('format');
    }

    /**
     * Returns the user abstraction associated with this subscription or null if no user has been associated yet.
     *
     * @return Magento_Outbound_UserInterface|null
     */
    public function getUser()
    {
        if ($this->getApiUserId() === null) {
            return null;
        }
        return $this->_userFactory->create($this->getApiUserId());
    }

    /**
     * Returns the type of authentication to use when attaching authentication to a message
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return $this->getData('authentication_type');
    }
}
