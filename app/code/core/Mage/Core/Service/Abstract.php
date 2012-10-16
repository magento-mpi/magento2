<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract Service Layer
 */
abstract class Mage_Core_Service_Abstract
{
    const PAGE_KEY = 'offset';
    const LIMIT_KEY = 'limit';
    const FILTER_KEY = 'filter';
    const FILTER_FIELD_KEY = 'attribute';
    const SORT_FIELD_KEY = 'sort_field';
    const SORT_ORDER_KEY = 'sort_order';
    const DEFAULT_SORT_ORDER = Varien_Data_Collection::SORT_ORDER_ASC;

    /**
     * @var Mage_Core_Helper_Abstract
     */
    protected $_translateHelper;

    /**
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Constructor
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_translateHelper = isset($args['helper']) ? $args['helper'] : Mage::helper('Mage_Core_Helper_Data');
        $this->_eventManager = isset($args['eventManager'])
            ? $args['eventManager'] : Mage::getSingleton('Mage_Core_Model_Event_Manager');
    }

    /**
     * Apply pager, sorting and filters to collection
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $data
     * @return Mage_Core_Service_Abstract
     * @throws InvalidArgumentException
     */
    protected function _prepareCollection(Varien_Data_Collection_Db $collection, array $data)
    {
        $this
            ->_applyCollectionPager($collection, $data)
            ->_applyCollectionSorting($collection, $data)
            ->_applyCollectionFilter($collection, $data);

        return $this;
    }

    /**
     * Apply collection pager
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $data
     * @return Mage_Core_Service_Abstract
     * @throws InvalidArgumentException
     */
    protected function _applyCollectionPager(Varien_Data_Collection_Db $collection, array $data)
    {
        if (isset($data[self::PAGE_KEY]) && isset($data[self::LIMIT_KEY])) {
            $page = (int)$data[self::PAGE_KEY] - 1;
            $limit = (int)$data[self::LIMIT_KEY];
            if ($page < 0) {
                throw new InvalidArgumentException($this->_translateHelper->__('Page size is incorrect'));
            }
            if ($limit < 1) {
                throw new InvalidArgumentException($this->_translateHelper->__('Offset is incorrect'));
            }
            $collection
                ->setCurPage($page)
                ->setPageSize($limit);
        } elseif (isset($data[self::PAGE_KEY]) || isset($data[self::LIMIT_KEY])) {
            throw new InvalidArgumentException($this->_translateHelper->__('Offset should be used with limit'));
        }
        return $this;
    }

    /**
     * Apply collection sort by rules
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $data
     * @return Mage_Core_Service_Abstract
     * @throws InvalidArgumentException
     */
    protected function _applyCollectionSorting(Varien_Data_Collection_Db $collection, array $data)
    {
        if (isset($data[self::SORT_FIELD_KEY])) {
            $dir = self::DEFAULT_SORT_ORDER;
            $allowedSortOrder = array(Varien_Data_Collection::SORT_ORDER_ASC, Varien_Data_Collection::SORT_ORDER_DESC);
            if (isset($data[self::SORT_ORDER_KEY])) {
                if (!in_array($data[self::SORT_ORDER_KEY], $allowedSortOrder)) {
                    throw new InvalidArgumentException($this->_translateHelper->__('Sort order is invalid'));
                }
                $dir = $data[self::SORT_ORDER_KEY];
            }

            $collection->setOrder($data[self::SORT_FIELD_KEY], $dir);
        }
        return $this;
    }

    /**
     * Validate filter data and apply it to collection if possible
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $data
     * @return Mage_Core_Service_Abstract
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    protected function _applyCollectionFilter(Varien_Data_Collection_Db $collection, array $data)
    {
        if (!isset($data[self::FILTER_KEY])) {
            return $this;
        }

        $filter = $data[self::FILTER_KEY];
        foreach ($filter as $filterEntry) {
            if (!is_array($filterEntry) || !array_key_exists(self::FILTER_FIELD_KEY, $filterEntry)) {
                throw new InvalidArgumentException($this->_translateHelper->__('Invalid filter'));
            }
            $attributeCode = $filterEntry[self::FILTER_FIELD_KEY];
            unset($filterEntry[self::FILTER_FIELD_KEY]);

            try {
                if (method_exists($collection, 'addAttributeToFilter')) {
                    $collection->addAttributeToFilter($attributeCode, $filterEntry);
                } elseif (method_exists($collection, 'addFieldToFilter')) {
                    $collection->addFieldToFilter($attributeCode, $filterEntry);
                }
            } catch(Exception $e) {
                throw new RuntimeException($this->_translateHelper->__('Error occurred during filtering collection'));
            }
        }
        return $this;
    }
}
