<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model;

use Magento\Framework\Model\Exception;

/**
 * TargetRule Rule Model
 *
 * @method \Magento\TargetRule\Model\Resource\Rule _getResource()
 * @method \Magento\TargetRule\Model\Resource\Rule getResource()
 * @method string getName()
 * @method \Magento\TargetRule\Model\Rule setName(string $value)
 * @method string getFromDate()
 * @method \Magento\TargetRule\Model\Rule setFromDate(string $value)
 * @method string getToDate()
 * @method \Magento\TargetRule\Model\Rule setToDate(string $value)
 * @method int getIsActive()
 * @method \Magento\TargetRule\Model\Rule setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method \Magento\TargetRule\Model\Rule setConditionsSerialized(string $value)
 * @method string getActionsSerialized()
 * @method \Magento\TargetRule\Model\Rule setActionsSerialized(string $value)
 * @method \Magento\TargetRule\Model\Rule setPositionsLimit(int $value)
 * @method int getApplyTo()
 * @method \Magento\TargetRule\Model\Rule setApplyTo(int $value)
 * @method int getSortOrder()
 * @method \Magento\TargetRule\Model\Rule setSortOrder(int $value)
 * @method int getUseCustomerSegment()
 * @method \Magento\TargetRule\Model\Rule setUseCustomerSegment(int $value)
 * @method string getActionSelect()
 * @method \Magento\TargetRule\Model\Rule setActionSelect(string $value)
 * @method array getCustomerSegmentIds()
 * @method \Magento\TargetRule\Model\Rule setCustomerSegmentIds(array $ids)
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Rule extends \Magento\Rule\Model\AbstractModel
{
    /**
     * Position behavior selectors
     */
    const BOTH_SELECTED_AND_RULE_BASED = 0;

    const SELECTED_ONLY = 1;

    const RULE_BASED_ONLY = 2;

    /**
     * Product list types
     */
    const RELATED_PRODUCTS = 1;

    const UP_SELLS = 2;

    const CROSS_SELLS = 3;

    /**
     * Shuffle mode by default
     */
    const ROTATION_SHUFFLE = 0;

    const ROTATION_NONE = 1;

    /**
     * Store default product positions limit
     */
    const POSITIONS_DEFAULT_LIMIT = 20;

    /**
     * Path to default values
     *
     * @deprecated after 1.11.2.0
     */
    const XML_PATH_DEFAULT_VALUES = 'catalog/magento_targetrule/';

    /**
     * Store matched products objects
     *
     * @var array
     */
    protected $_products;

    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds;

    /**
     * Store flags per store is applicable rule by date
     *
     * @var array
     */
    protected $_checkDateForStore = array();

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\TargetRule\Model\Rule\Condition\CombineFactory
     */
    protected $_ruleFactory;

    /**
     * @var \Magento\TargetRule\Model\Actions\Condition\CombineFactory
     */
    protected $_actionFactory;

    /**
     * @var \Magento\Framework\Model\Resource\Iterator
     */
    protected $_iterator;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Model\Resource\Iterator $iterator
     * @param \Magento\TargetRule\Model\Rule\Condition\CombineFactory $ruleFactory
     * @param \Magento\TargetRule\Model\Actions\Condition\CombineFactory $actionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\Resource\Iterator $iterator,
        \Magento\TargetRule\Model\Rule\Condition\CombineFactory $ruleFactory,
        \Magento\TargetRule\Model\Actions\Condition\CombineFactory $actionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_localeDate = $localeDate;
        $this->_iterator = $iterator;
        $this->_productFactory = $productFactory;
        $this->_ruleFactory = $ruleFactory;
        $this->_actionFactory = $actionFactory;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Set resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\TargetRule\Model\Resource\Rule');
    }

    /**
     * Reset action cached select if actions conditions has changed
     *
     * @return \Magento\TargetRule\Model\Rule
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if ($this->dataHasChangedFor('actions_serialized')) {
            $this->setData('action_select', null);
            $this->setData('action_select_bind', null);
        }

        return $this;
    }

    /**
     * Getter for rule combine conditions instance
     *
     * @return \Magento\TargetRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_ruleFactory->create();
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return \Magento\TargetRule\Model\Actions\Condition\Combine
     */
    public function getActionsInstance()
    {
        return $this->_actionFactory->create();
    }

    /**
     * Get options for `Apply to` field
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAppliesToOptions($withEmpty = false)
    {
        $result = array();
        if ($withEmpty) {
            $result[''] = __('-- Please Select --');
        }
        $result[\Magento\TargetRule\Model\Rule::RELATED_PRODUCTS] = __('Related Products');
        $result[\Magento\TargetRule\Model\Rule::UP_SELLS] = __('Up-sells');
        $result[\Magento\TargetRule\Model\Rule::CROSS_SELLS] = __('Cross-sells');

        return $result;
    }

    /**
     * Retrieve array of product objects which are matched by rule
     *
     * @param bool $onlyId
     * @return $this
     */
    public function prepareMatchingProducts($onlyId = false)
    {
        $productCollection = $this->_productFactory->create()->getCollection();

        if (!$onlyId && !is_null($this->_productIds)) {
            $productCollection->addIdFilter($this->_productIds);
            $this->_products = $productCollection->getItems();
        } else {
            $this->setCollectedAttributes(array());
            $this->getConditions()->collectValidatedAttributes($productCollection);

            $this->_productIds = array();
            $this->_products = array();
            $this->_iterator->walk(
                $productCollection->getSelect(),
                array(array($this, 'callbackValidateProduct')),
                array(
                    'attributes' => $this->getCollectedAttributes(),
                    'product' => $this->_productFactory->create(),
                    'onlyId' => (bool)$onlyId
                )
            );
        }

        return $this;
    }

    /**
     * Retrieve array of product objects which are matched by rule
     *
     * @return array
     * @deprecated
     */
    public function getMatchingProducts()
    {
        if (is_null($this->_products)) {
            $this->prepareMatchingProducts();
        }

        return $this->_products;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
            if (!key_exists('onlyId', $args) || !$args['onlyId']) {
                $this->_products[] = $product;
            }
        }
    }

    /**
     * Retrieve array of product Ids that are matched by rule
     *
     * @return array
     */
    public function getMatchingProductIds()
    {
        if (is_null($this->_productIds)) {
            $this->getMatchingProducts();
        }

        return $this->_productIds;
    }

    /**
     * Check if rule is applicable by date for specified store
     *
     * @param int $storeId
     * @return bool
     */
    public function checkDateForStore($storeId)
    {
        if (!isset($this->_checkDateForStore[$storeId])) {
            $this->_checkDateForStore[$storeId] = $this->_localeDate->isScopeDateInInterval(
                null,
                $this->getFromDate(),
                $this->getToDate()
            );
        }
        return $this->_checkDateForStore[$storeId];
    }

    /**
     * Get product positions for current rule
     *
     * @return int If positions limit is not set, then default limit will be returned
     */
    public function getPositionsLimit()
    {
        $limit = $this->getData('positions_limit');
        if (!$limit) {
            $limit = 20;
        }

        return $limit;
    }

    /**
     * Retrieve Action select bind array
     *
     * @return mixed
     */
    public function getActionSelectBind()
    {
        $bind = $this->getData('action_select_bind');
        if ($bind && is_string($bind)) {
            $bind = unserialize($bind);
        }

        return $bind;
    }

    /**
     * Set action select bind array or serialized string
     *
     * @param array|string $bind
     * @return $this
     */
    public function setActionSelectBind($bind)
    {
        if (is_array($bind)) {
            $bind = serialize($bind);
        }
        return $this->setData('action_select_bind', $bind);
    }

    /**
     * Validate rule data
     *
     * @param \Magento\Framework\Object $object
     * @return string[]|bool - Return true if validation passed successfully. Array with errors description otherwise
     * @throws Exception
     */
    public function validateData(\Magento\Framework\Object $object)
    {
        $result = parent::validateData($object);

        if (!is_array($result)) {
            $result = array();
        }

        $validator = new \Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z0-9_\/]{1,255}$/'));
        $actionArgsList = $object->getData('rule');
        if (is_array($actionArgsList) && isset($actionArgsList['actions'])) {
            foreach ($actionArgsList['actions'] as $actionArgsIndex => $actionArgs) {
                if (1 === $actionArgsIndex) {
                    continue;
                }
                if (!class_exists($actionArgs['type'])) {
                    throw new Exception(__('Model class name for attribute is invalid'));
                }
                if (isset($actionArgs['attribute']) && !$validator->isValid($actionArgs['attribute'])) {
                    $result[] = __(
                        'This attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscores (_), and be sure the code begins with a letter.'
                    );
                }
            }
        }

        return !empty($result) ? $result : true;
    }

    /**
     * Retrieve Customer Segment Relations
     *
     * @return array
     * @deprecated after 1.11.2.0
     */
    public function getCustomerSegmentRelations()
    {
        return array();
    }

    /**
     * Set customer segment relations
     *
     * @param array|string $relations
     * @return $this
     * @deprecated after 1.11.2.0
     */
    public function setCustomerSegmentRelations($relations)
    {
        return $this;
    }
}
