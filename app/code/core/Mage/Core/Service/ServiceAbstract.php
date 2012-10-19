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
abstract class Mage_Core_Service_ServiceAbstract
{
    /** #@+
     * Constants for specifying rules of paging, sorting and filtering collection
     */
    const PAGE_KEY = 'page';
    const LIMIT_KEY = 'limit';
    const FILTER_KEY = 'filter';
    const FILTER_FIELD_KEY = 'attribute';
    const SORT_FIELD_KEY = 'order';
    const SORT_ORDER_KEY = 'dir';
    const DEFAULT_SORT_ORDER = Varien_Data_Collection::SORT_ORDER_ASC;
    /** #@- */

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Validator_Config
     */
    protected $_validatorFactory;

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
        $this->_config = isset($args['config'])
            ? $args['config'] : Mage::getConfig();

        $this->_translateHelper = isset($args['helper']) ? $args['helper'] : Mage::helper('Mage_Core_Helper_Data');

        $this->_eventManager = isset($args['eventManager'])
            ? $args['eventManager'] : Mage::getSingleton('Mage_Core_Model_Event_Manager');

        if (isset($args['validatorFactory'])) {
            $this->_validatorFactory = $args['validatorFactory'];
        } else {
            $configFiles = $this->_config->getModuleConfigurationFiles('validation.xml');
            $this->_validatorFactory = new Magento_Validator_Config($configFiles);
        }
    }


    /**
     * Apply pager, sorting and filters to collection
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $data
     * @return Mage_Core_Service_ServiceAbstract
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
     * @return Mage_Core_Service_ServiceAbstract
     * @throws InvalidArgumentException
     */
    protected function _applyCollectionPager(Varien_Data_Collection_Db $collection, array $data)
    {
        if (isset($data[self::LIMIT_KEY])) {
            $page = isset($data[self::PAGE_KEY]) ? (int)$data[self::PAGE_KEY] : 1;
            $limit = (int)$data[self::LIMIT_KEY];
            if ($page < 1) {
                throw new InvalidArgumentException($this->_translateHelper->__('Page number is incorrect'));
            }
            if ($limit < 1) {
                throw new InvalidArgumentException($this->_translateHelper->__('Limit is incorrect'));
            }
            $collection->setCurPage($page);
            $collection->setPageSize($limit);
        } elseif (isset($data[self::PAGE_KEY])) {
            throw new InvalidArgumentException($this->_translateHelper->__('Page number must be used with limit'));
        }
        return $this;
    }

    /**
     * Apply collection sort by rules
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $data
     * @return Mage_Core_Service_ServiceAbstract
     * @throws InvalidArgumentException
     */
    protected function _applyCollectionSorting(Varien_Data_Collection_Db $collection, array $data)
    {
        if (isset($data[self::SORT_FIELD_KEY])) {
            $dir = self::DEFAULT_SORT_ORDER;
            $allowedSortOrder = array(Varien_Data_Collection::SORT_ORDER_ASC, Varien_Data_Collection::SORT_ORDER_DESC);
            if (isset($data[self::SORT_ORDER_KEY])) {
                if (!in_array(strtoupper($data[self::SORT_ORDER_KEY]), $allowedSortOrder)) {
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
     * @return Mage_Core_Service_ServiceAbstract
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    protected function _applyCollectionFilter(Varien_Data_Collection_Db $collection, array $data)
    {
        if (!isset($data[self::FILTER_KEY])) {
            return $this;
        }

        $filter = $data[self::FILTER_KEY];
        if (!is_array($filter)) {
            throw new InvalidArgumentException($this->_translateHelper->__('Invalid filter format'));
        }
        foreach ($filter as $filterEntry) {
            if (!is_array($filterEntry) || !array_key_exists(self::FILTER_FIELD_KEY, $filterEntry)) {
                throw new InvalidArgumentException($this->_translateHelper->__('Invalid filter format'));
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
                throw new RuntimeException($this->_translateHelper->__('Error occurred during filtering data'));
            }
        }
        return $this;
    }

    /**
     * Remove forbidden fields for specified action
     *
     * @param string $module
     * @param string $action
     * @param array $data
     */
    protected function _removeForbiddenFields($module, $action, &$data)
    {
        $forbiddenFields = $this->_getForbiddenFields($module, $action);
        if (!empty($forbiddenFields)) {
            foreach (array_keys($data) as $dataKey) {
                if (in_array($dataKey, $forbiddenFields)) {
                    unset($data[$dataKey]);
                }
            }
        }
    }

    /**
     * Get forbidden fields for specified action
     *
     * @param string $module
     * @param string $action
     * @return array
     */
    protected function _getForbiddenFields($module, $action)
    {
        $forbiddenFields = array();

        $xmlPath = sprintf(self::XML_CONFIG_FORBIDDEN_FIELDS_PATH, $module, $action);
        /** @var Mage_Core_Model_Config_Element $forbiddenFields */
        $forbiddenFieldsNodes = Mage::getConfig()->getNode($xmlPath);
        if (!empty($forbiddenFieldsNodes)) {
            $forbiddenFields = array_keys($forbiddenFieldsNodes->asArray());
        }

        return $forbiddenFields;
    }
}
