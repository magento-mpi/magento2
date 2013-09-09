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
abstract class Magento_TargetRule_Block_Product_Abstract extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Link collection
     *
     * @var null|Magento_Catalog_Model_Resource_Product_Collection
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
     * @return Magento_Catalog_Model_Resource_Product_Collection
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
     * @var Magento_TargetRule_Helper_Data
     */
    protected $_targetRuleData = null;

    /**
     * @param Magento_TargetRule_Helper_Data $targetRuleData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_TargetRule_Helper_Data $targetRuleData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_targetRuleData = $targetRuleData;
        parent::__construct($taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Return the behavior positions applicable to products based on the rule(s)
     *
     * @return array
     */
    public function getRuleBasedBehaviorPositions()
    {
        return array(
            Magento_TargetRule_Model_Rule::BOTH_SELECTED_AND_RULE_BASED,
            Magento_TargetRule_Model_Rule::RULE_BASED_ONLY,
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
            Magento_TargetRule_Model_Rule::BOTH_SELECTED_AND_RULE_BASED,
            Magento_TargetRule_Model_Rule::SELECTED_ONLY,
        );
    }

    /**
     * Get link collection
     *
     * @return Magento_Catalog_Model_Resource_Product_Collection|null
     */
    public function getLinkCollection()
    {
        if (is_null($this->_linkCollection)) {
            $this->_linkCollection = $this->_getTargetLinkCollection();

            if ($this->_linkCollection) {
                // Perform rotation mode
                $select = $this->_linkCollection->getSelect();
                $rotationMode = $this->_targetRuleData->getRotationMode($this->getProductListType());
                if ($rotationMode == Magento_TargetRule_Model_Rule::ROTATION_SHUFFLE) {
                    Mage::getResourceSingleton('Magento_TargetRule_Model_Resource_Index')->orderRand($select);
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
        return $rotationMode == Magento_TargetRule_Model_Rule::ROTATION_SHUFFLE;
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
     * @param Magento_Object $item1
     * @param Magento_Object $item2
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
     * @return Magento_TargetRule_Block_Product_Abstract
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
