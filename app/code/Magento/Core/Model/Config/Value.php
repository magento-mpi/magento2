<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Config;

/**
 * Config data model
 *
 * @method \Magento\Core\Model\Resource\Config\Data _getResource()
 * @method \Magento\Core\Model\Resource\Config\Data getResource()
 * @method string getScope()
 * @method \Magento\App\Config\ValueInterface setScope(string $value)
 * @method int getScopeId()
 * @method \Magento\App\Config\ValueInterface setScopeId(int $value)
 * @method string getPath()
 * @method \Magento\App\Config\ValueInterface setPath(string $value)
 * @method string getValue()
 * @method \Magento\App\Config\ValueInterface setValue(string $value)
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Value extends \Magento\Core\Model\AbstractModel implements \Magento\App\Config\ValueInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_config_data';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'config_data';

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Magento model constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Resource\Config\Data');
    }

    /**
     * Add availability call after load as public
     *
     * @return void
     */
    public function afterLoad()
    {
        $this->_afterLoad();
    }

    /**
     * Check if config data value was changed
     *
     * @return bool
     */
    public function isValueChanged()
    {
        return $this->getValue() != $this->getOldValue();
    }

    /**
     * Get old value from existing config
     *
     * @return string
     */
    public function getOldValue()
    {
        $storeCode   = $this->getStoreCode();
        $websiteCode = $this->getWebsiteCode();
        $path        = $this->getPath();

        if ($storeCode) {
            return $this->_storeManager->getStore($storeCode)->getConfig($path);
        }
        if ($websiteCode) {
            return $this->_storeManager->getWebsite($websiteCode)->getConfig($path);
        }
        return (string) $this->_config->getValue($path, \Magento\BaseScopeInterface::SCOPE_DEFAULT);
    }


    /**
     * Get value by key for new user data from <section>/groups/<group>/fields/<field>
     *
     * @param string $key
     * @return string
     */
    public function getFieldsetDataValue($key)
    {
        $data = $this->_getData('fieldset_data');
        return (is_array($data) && isset($data[$key])) ? $data[$key] : null;
    }
}
