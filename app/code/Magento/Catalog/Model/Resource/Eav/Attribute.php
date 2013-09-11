<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog attribute model
 *
 * @method \Magento\Catalog\Model\Resource\Attribute _getResource()
 * @method \Magento\Catalog\Model\Resource\Attribute getResource()
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getFrontendInputRenderer()
 * @method string setFrontendInputRenderer(string $value)
 * @method int setIsGlobal(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getIsVisible()
 * @method int setIsVisible(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getIsSearchable()
 * @method int setIsSearchable(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getSearchWeight()
 * @method int setSearchWeight(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getIsFilterable()
 * @method int setIsFilterable(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getIsComparable()
 * @method int setIsComparable(int $value)
 * @method int setIsVisibleOnFront(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getIsHtmlAllowedOnFront()
 * @method int setIsHtmlAllowedOnFront(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getIsUsedForPriceRules()
 * @method int setIsUsedForPriceRules(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getIsFilterableInSearch()
 * @method int setIsFilterableInSearch(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getUsedInProductListing()
 * @method int setUsedInProductListing(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getUsedForSortBy()
 * @method int setUsedForSortBy(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getIsConfigurable()
 * @method int setIsConfigurable(int $value)
 * @method string setApplyTo(string $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getIsVisibleInAdvancedSearch()
 * @method int setIsVisibleInAdvancedSearch(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getPosition()
 * @method int setPosition(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getIsWysiwygEnabled()
 * @method int setIsWysiwygEnabled(int $value)
 * @method \Magento\Catalog\Model\Resource\Eav\Attribute getIsUsedForPromoRules()
 * @method int setIsUsedForPromoRules(int $value)
 * @method string getFrontendLabel()
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Eav;

class Attribute extends \Magento\Eav\Model\Entity\Attribute
{
    const SCOPE_STORE                           = 0;
    const SCOPE_GLOBAL                          = 1;
    const SCOPE_WEBSITE                         = 2;

    const MODULE_NAME                           = 'Magento_Catalog';
    const ENTITY                                = 'catalog_eav_attribute';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix                     = 'catalog_entity_attribute';
    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject                     = 'attribute';

    /**
     * Array with labels
     *
     * @var array
     */
    static protected $_labels                   = null;

    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\Resource\Attribute');
    }

    /**
     * Processing object before save data
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        $this->setData('modulePrefix', self::MODULE_NAME);
        if (isset($this->_origData['is_global'])) {
            if (!isset($this->_data['is_global'])) {
                $this->_data['is_global'] = self::SCOPE_GLOBAL;
            }
            if (($this->_data['is_global'] != $this->_origData['is_global'])
                && $this->_getResource()->isUsedBySuperProducts($this)) {
                \Mage::throwException(__('Do not change the scope. This attribute is used in configurable products.'));
            }
        }
        if ($this->getFrontendInput() == 'price') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('\Magento\Catalog\Model\Product\Attribute\Backend\Price');
            }
        }
        if ($this->getFrontendInput() == 'textarea') {
            if ($this->getIsWysiwygEnabled()) {
                $this->setIsHtmlAllowedOnFront(1);
            }
        }
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _afterSave()
    {
        /**
         * Fix saving attribute in admin
         */
        \Mage::getSingleton('Magento\Eav\Model\Config')->clear();
        \Mage::getSingleton('Magento\Index\Model\Indexer')->processEntityAction(
            $this, self::ENTITY, \Magento\Index\Model\Event::TYPE_SAVE
        );
        return parent::_afterSave();
    }

    /**
     * Register indexing event before delete catalog eav attribute
     *
     * @return \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    protected function _beforeDelete()
    {
        if ($this->_getResource()->isUsedBySuperProducts($this)) {
            \Mage::throwException(__('This attribute is used in configurable products.'));
        }
        \Mage::getSingleton('Magento\Index\Model\Indexer')->logEvent(
            $this, self::ENTITY, \Magento\Index\Model\Event::TYPE_DELETE
        );
        return parent::_beforeDelete();
    }

    /**
     * Init indexing process after catalog eav attribute delete commit
     *
     * @return \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();
        \Mage::getSingleton('Magento\Index\Model\Indexer')->indexEvents(
            self::ENTITY, \Magento\Index\Model\Event::TYPE_DELETE
        );
        return $this;
    }

    /**
     * Return is attribute global
     *
     * @return integer
     */
    public function getIsGlobal()
    {
        return $this->_getData('is_global');
    }

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    /**
     * Retrieve attribute is website scope website
     *
     * @return bool
     */
    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    /**
     * Retrieve store id
     *
     * @return int
     */
    public function getStoreId()
    {
        $dataObject = $this->getDataObject();
        if ($dataObject) {
            return $dataObject->getStoreId();
        }
        return $this->getData('store_id');
    }

    /**
     * Retrieve apply to products array
     * Return empty array if applied to all products
     *
     * @return array
     */
    public function getApplyTo()
    {
        if ($this->getData('apply_to')) {
            if (is_array($this->getData('apply_to'))) {
                return $this->getData('apply_to');
            }
            return explode(',', $this->getData('apply_to'));
        } else {
            return array();
        }
    }

    /**
     * Retrieve source model
     *
     * @return \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
     */
    public function getSourceModel()
    {
        $model = $this->getData('source_model');
        if (empty($model)) {
            if ($this->getBackendType() == 'int' && $this->getFrontendInput() == 'select') {
                return $this->_getDefaultSourceModel();
            }
        }
        return $model;
    }

    /**
     * Whether allowed for rule condition
     *
     * @return bool
     */
    public function isAllowedForRuleCondition()
    {
        $allowedInputTypes = array(
            'boolean',
            'date',
            'datetime',
            'multiselect',
            'price',
            'select',
            'text',
            'textarea',
            'weight',
        );
        return $this->getIsVisible() && in_array($this->getFrontendInput(), $allowedInputTypes);
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function _getDefaultSourceModel()
    {
        return '\Magento\Eav\Model\Entity\Attribute\Source\Table';
    }

    /**
     * Check is an attribute used in EAV index
     *
     * @return bool
     */
    public function isIndexable()
    {
        // exclude price attribute
        if ($this->getAttributeCode() == 'price') {
            return false;
        }

        if (!$this->getIsFilterableInSearch() && !$this->getIsVisibleInAdvancedSearch() && !$this->getIsFilterable()) {
            return false;
        }

        $backendType    = $this->getBackendType();
        $frontendInput  = $this->getFrontendInput();

        if ($backendType == 'int' && $frontendInput == 'select') {
            return true;
        } else if ($backendType == 'varchar' && $frontendInput == 'multiselect') {
            return true;
        } else if ($backendType == 'decimal') {
            return true;
        }

        return false;
    }

    /**
     * Retrieve index type for indexable attribute
     *
     * @return string|false
     */
    public function getIndexType()
    {
        if (!$this->isIndexable()) {
            return false;
        }
        if ($this->getBackendType() == 'decimal') {
            return 'decimal';
        }

        return 'source';
    }
}
