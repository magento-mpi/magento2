<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule abstract Products Block
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
namespace Magento\TargetRule\Block\Product;

abstract class AbstractProduct extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Link collection
     *
     * @var null|\Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $_linkCollection = null;

    /**
     * Catalog Product List Item Collection array
     *
     * @var null|array
     */
    protected $_items = null;

    /**
     * Get link collection for specific target
     *
     * @abstract
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    abstract protected function _getTargetLinkCollection();

    /**
     * Get target rule products
     *
     * @abstract
     * @return array
     */
    abstract protected function _getTargetRuleProducts();

    /**
     * Retrieve Catalog Product List Type identifier
     *
     * @return int
     */
    abstract public function getProductListType();

    /**
     * Retrieve Maximum Number Of Product
     *
     * @return int
     */
    abstract public function getPositionLimit();

    /**
     * Retrieve Position Behavior
     *
     * @return int
     */
    abstract public function getPositionBehavior();

    /**
     * Target rule data
     *
     * @var \Magento\TargetRule\Helper\Data
     */
    protected $_targetRuleData = null;

    /**
     * @var \Magento\TargetRule\Model\Resource\Index
     */
    protected $_resourceIndex;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\TargetRule\Model\Resource\Index $index
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\TargetRule\Helper\Data $targetRuleData
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\TargetRule\Model\Resource\Index $index,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\TargetRule\Helper\Data $targetRuleData,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_resourceIndex = $index;
        $this->_targetRuleData = $targetRuleData;
        parent::__construct($storeManager, $catalogConfig, $coreRegistry, $taxData, $catalogData, $coreData,
            $context, $data);
    }

    /**
     * Return the behavior positions applicable to products based on the rule(s)
     *
     * @return array
     */
    public function getRuleBasedBehaviorPositions()
    {
        return array(
            \Magento\TargetRule\Model\Rule::BOTH_SELECTED_AND_RULE_BASED,
            \Magento\TargetRule\Model\Rule::RULE_BASED_ONLY,
        );
    }

    /**
     * Retrieve the behavior positions applicable to selected products
     *
     * @return array
     */
    public function getSelectedBehaviorPositions()
    {
        return array(
            \Magento\TargetRule\Model\Rule::BOTH_SELECTED_AND_RULE_BASED,
            \Magento\TargetRule\Model\Rule::SELECTED_ONLY,
        );
    }

    /**
     * Get link collection
     *
     * @return \Magento\Catalog\Model\Resource\Product\Collection|null
     */
    public function getLinkCollection()
    {
        if (is_null($this->_linkCollection)) {
            $this->_linkCollection = $this->_getTargetLinkCollection();

            if ($this->_linkCollection) {
                // Perform rotation mode
                $select = $this->_linkCollection->getSelect();
                $rotationMode = $this->_targetRuleData->getRotationMode($this->getProductListType());
                if ($rotationMode == \Magento\TargetRule\Model\Rule::ROTATION_SHUFFLE) {
                    $this->_resourceIndex->orderRand($select);
                } else {
                    $select->order('link_attribute_position_int.value ASC');
                }
            }
        }

        return $this->_linkCollection;
    }

    /**
     * Get linked products
     *
     * @return array
     */
    protected function _getLinkProducts()
    {
        $items = array();
        $linkCollection = $this->getLinkCollection();
        if ($linkCollection) {
            foreach ($linkCollection as $item) {
                $items[$item->getEntityId()] = $item;
            }
        }
        return $items;
    }

    /**
     * Whether rotation mode is set to "shuffle"
     *
     * @return bool
     */
    public function isShuffled()
    {
        $rotationMode = $this->_targetRuleData->getRotationMode($this->getProductListType());
        return $rotationMode == \Magento\TargetRule\Model\Rule::ROTATION_SHUFFLE;
    }

    /**
     * Order product items
     *
     * @return array|null
     */
    protected function _orderProductItems()
    {
        if (!is_null($this->_items)) {
            if ($this->isShuffled()) {
                // shuffling assoc
                $ids = array_keys($this->_items);
                shuffle($ids);
                $items = $this->_items;
                $this->_items = array();
                foreach ($ids as $id) {
                    $this->_items[$id] = $items[$id];
                }
            } else {
                uasort($this->_items, array($this, 'compareItems'));
            }
            $this->_sliceItems();
        }
        return $this->_items;
    }

    /**
     * Compare two items for ordered list
     *
     * @param \Magento\Object $item1
     * @param \Magento\Object $item2
     * @return int
     */
    public function compareItems($item1, $item2)
    {
        // Prevent rule-based items to have any position
        if (is_null($item2->getPosition()) && !is_null($item1->getPosition())) {
            return -1;
        } elseif (is_null($item1->getPosition()) && !is_null($item2->getPosition())) {
            return 1;
        }
        $positionDiff = (int)$item1->getPosition() - (int)$item2->getPosition();
        if ($positionDiff != 0) {
            return $positionDiff;
        }
        return (int)$item1->getEntityId() - (int)$item2->getEntityId();
    }

    /**
     * Slice items to limit
     *
     * @return \Magento\TargetRule\Block\Product\AbstractProduct
     */
    protected function _sliceItems()
    {
        if (is_null($this->_items)) {
            return $this;
        }
        $i = 0;
        foreach ($this->_items as $id => $item) {
            ++$i;
            if ($i > $this->getPositionLimit()) {
                unset($this->_items[$id]);
            }
        }

        return $this;
    }

    /**
     * Retrieve Catalog Product List Items
     *
     * @return array
     */
    public function getItemCollection()
    {
        if (is_null($this->_items)) {
            $behavior   = $this->getPositionBehavior();

            $this->_items = array();

            if (in_array($behavior, $this->getRuleBasedBehaviorPositions())) {
                $this->_items = $this->_getTargetRuleProducts();
            }

            if (in_array($behavior, $this->getSelectedBehaviorPositions())) {
                foreach ($this->_getLinkProducts() as $id => $item) {
                    $this->_items[$id] = $item;
                }
            }
            $this->_orderProductItems();
        }

        return $this->_items;
    }
}
