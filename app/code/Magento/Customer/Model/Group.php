<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model;

use Magento\Customer\Api\Data\GroupDataBuilder;
use Magento\Framework\Api\AttributeDataBuilder;

/**
 * Customer group model
 *
 * @method \Magento\Customer\Model\Resource\Group _getResource()
 * @method \Magento\Customer\Model\Resource\Group getResource()
 * @method string getCustomerGroupCode()
 * @method \Magento\Customer\Model\Group setCustomerGroupCode(string $value)
 * @method \Magento\Customer\Model\Group setTaxClassId(int $value)
 * @method Group setTaxClassName(string $value)
 */
class Group extends \Magento\Framework\Model\AbstractExtensibleModel
{
    const NOT_LOGGED_IN_ID = 0;

    const CUST_GROUP_ALL = 32000;

    const ENTITY = 'customer_group';

    const GROUP_CODE_MAX_LENGTH = 32;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'customer_group';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'object';

    /**
     * @var \Magento\Store\Model\StoresConfig
     */
    protected $_storesConfig;

    /**
     * @var GroupDataBuilder
     */
    protected $groupBuilder;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Tax\Model\ClassModelFactory
     */
    protected $classModelFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\MetadataServiceInterface $metadataService
     * @param \Magento\Store\Model\StoresConfig $storesConfig
     * @param GroupDataBuilder $groupBuilder
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Tax\Model\ClassModelFactory $classModelFactory
     * @param AttributeDataBuilder $customAttributeBuilder
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\MetadataServiceInterface $metadataService,
        \Magento\Store\Model\StoresConfig $storesConfig,
        GroupDataBuilder $groupBuilder,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Tax\Model\ClassModelFactory $classModelFactory,
        AttributeDataBuilder $customAttributeBuilder,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storesConfig = $storesConfig;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->groupBuilder = $groupBuilder;
        $this->classModelFactory = $classModelFactory;
        parent::__construct(
            $context,
            $registry,
            $metadataService,
            $customAttributeBuilder,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Customer\Model\Resource\Group');
    }

    /**
     * Alias for setCustomerGroupCode
     *
     * @param string $value
     * @return $this
     */
    public function setCode($value)
    {
        return $this->setCustomerGroupCode($value);
    }

    /**
     * Alias for getCustomerGroupCode
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getCustomerGroupCode();
    }

    /**
     * Get tax class name
     *
     * @return string
     */
    public function getTaxClassName()
    {
        $taxClassName = $this->getData('tax_class_name');
        if ($taxClassName) {
            return $taxClassName;
        }
        $classModel = $this->classModelFactory->create();
        $classModel->load($this->getTaxClassId());
        $taxClassName = $classModel->getClassName();
        $this->setData('tax_class_name', $taxClassName);
        return $taxClassName;
    }

    /**
     * Determine if this group is used as the create account default group
     *
     * @return bool
     */
    public function usesAsDefault()
    {
        $data = $this->_storesConfig->getStoresConfigByPath(
            GroupManagement::XML_PATH_DEFAULT_ID
        );
        if (in_array($this->getId(), $data)) {
            return true;
        }
        return false;
    }

    /**
     * Prepare data before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        $this->_prepareData();
        return parent::beforeSave();
    }

    /**
     * Prepare customer group data
     *
     * @return $this
     */
    protected function _prepareData()
    {
        $this->setCode(substr($this->getCode(), 0, self::GROUP_CODE_MAX_LENGTH));
        return $this;
    }
}
