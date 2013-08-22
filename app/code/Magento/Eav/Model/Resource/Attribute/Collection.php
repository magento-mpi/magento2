<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV additional attribute resource collection (Using Forms)
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Eav_Model_Resource_Attribute_Collection
    extends Magento_Eav_Model_Resource_Entity_Attribute_Collection
{
    /**
     * code of password hash in customer's EAV tables
     */
    const EAV_CODE_PASSWORD_HASH = 'password_hash';

    /**
     * Current website scope instance
     *
     * @var Magento_Core_Model_Website
     */
    protected $_website;

    /**
     * Attribute Entity Type Filter
     *
     * @var Magento_Eav_Model_Entity_Type
     */
    protected $_entityType;

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    abstract protected function _getEntityTypeCode();

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    abstract protected function _getEavWebsiteTable();

    /**
     * Get default attribute entity type code
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return $this->_getEntityTypeCode();
    }

    /**
     * Return eav entity type instance
     *
     * @return Magento_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        if ($this->_entityType === null) {
            $this->_entityType = Mage::getSingleton('Magento_Eav_Model_Config')
                ->getEntityType($this->_getEntityTypeCode());
        }
        return $this->_entityType;
    }

    /**
     * Set Website scope
     *
     * @param Magento_Core_Model_Website|int $website
     * @return Magento_Eav_Model_Resource_Attribute_Collection
     */
    public function setWebsite($website)
    {
        $this->_website = Mage::app()->getWebsite($website);
        $this->addBindParam('scope_website_id', $this->_website->getId());
        return $this;
    }

    /**
     * Return current website scope instance
     *
     * @return Magento_Core_Model_Website
     */
    public function getWebsite()
    {
        if ($this->_website === null) {
            $this->_website = Mage::app()->getStore()->getWebsite();
        }
        return $this->_website;
    }

    /**
     * Initialize collection select
     *
     * @return Magento_Eav_Model_Resource_Attribute_Collection
     */
    protected function _initSelect()
    {
        $select         = $this->getSelect();
        $connection     = $this->getConnection();
        $entityType     = $this->getEntityType();
        $extraTable     = $entityType->getAdditionalAttributeTable();
        $mainDescribe   = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        $mainColumns    = array();

        foreach (array_keys($mainDescribe) as $columnName) {
            $mainColumns[$columnName] = $columnName;
        }

        $select->from(array('main_table' => $this->getResource()->getMainTable()), $mainColumns);

        // additional attribute data table
        $extraDescribe  = $connection->describeTable($this->getTable($extraTable));
        $extraColumns   = array();
        foreach (array_keys($extraDescribe) as $columnName) {
            if (isset($mainColumns[$columnName])) {
                continue;
            }
            $extraColumns[$columnName] = $columnName;
        }

        $this->addBindParam('mt_entity_type_id', (int)$entityType->getId());
        $select
            ->join(
                array('additional_table' => $this->getTable($extraTable)),
                'additional_table.attribute_id = main_table.attribute_id',
                $extraColumns)
            ->where('main_table.entity_type_id = :mt_entity_type_id');

        // scope values

        $scopeDescribe  = $connection->describeTable($this->_getEavWebsiteTable());
        unset($scopeDescribe['attribute_id']);
        $scopeColumns   = array();
        foreach (array_keys($scopeDescribe) as $columnName) {
            if ($columnName == 'website_id') {
                $scopeColumns['scope_website_id'] = $columnName;
            } else {
                if (isset($mainColumns[$columnName])) {
                    $alias = sprintf('scope_%s', $columnName);
                    $expression = $connection->getCheckSql('main_table.%s IS NULL',
                        'scope_table.%s', 'main_table.%s');
                    $expression = sprintf($expression, $columnName, $columnName, $columnName);
                    $this->addFilterToMap($columnName, $expression);
                    $scopeColumns[$alias] = $columnName;
                } elseif (isset($extraColumns[$columnName])) {
                    $alias = sprintf('scope_%s', $columnName);
                    $expression = $connection->getCheckSql('additional_table.%s IS NULL',
                        'scope_table.%s', 'additional_table.%s');
                    $expression = sprintf($expression, $columnName, $columnName, $columnName);
                    $this->addFilterToMap($columnName, $expression);
                    $scopeColumns[$alias] = $columnName;
                }
            }
        }

        $select->joinLeft(
            array('scope_table' => $this->_getEavWebsiteTable()),
            'scope_table.attribute_id = main_table.attribute_id AND scope_table.website_id = :scope_website_id',
            $scopeColumns
        );
        $websiteId = $this->getWebsite() ? (int)$this->getWebsite()->getId() : 0;
        $this->addBindParam('scope_website_id', $websiteId);

        return $this;
    }

    /**
     * Specify attribute entity type filter.
     * Entity type is defined.
     *
     * @param  int $type
     * @return Magento_Eav_Model_Resource_Attribute_Collection
     */
    public function setEntityTypeFilter($type)
    {
        return $this;
    }

    /**
     * Specify filter by "is_visible" field
     *
     * @return Magento_Eav_Model_Resource_Attribute_Collection
     */
    public function addVisibleFilter()
    {
        return $this->addFieldToFilter('is_visible', 1);
    }

    /**
     * Exclude system hidden attributes
     *
     * @return Magento_Eav_Model_Resource_Attribute_Collection
     */
    public function addSystemHiddenFilter()
    {
        $connection = $this->getConnection();
        $expression = $connection->getCheckSql('additional_table.is_system = 1 AND additional_table.is_visible = 0',
            '1', '0');
        $this->getSelect()->where($connection->quoteInto($expression . ' = ?', 0));
        return $this;
    }

    /**
     * Exclude system hidden attributes but include password hash
     *
     * @return Magento_Customer_Model_Resource_Attribute_Collection
     */
    public function addSystemHiddenFilterWithPasswordHash()
    {
        $connection = $this->getConnection();
        $expression = $connection->getCheckSql(
            $connection->quoteInto(
                'additional_table.is_system = 1 AND additional_table.is_visible = 0 AND main_table.attribute_code != ?',
                self::EAV_CODE_PASSWORD_HASH
            ),
            '1', '0'
        );
        $this->getSelect()->where($connection->quoteInto($expression . ' = ?', 0));
        return $this;
    }

    /**
     * Add exclude hidden frontend input attribute filter to collection
     *
     * @return Magento_Eav_Model_Resource_Attribute_Collection
     */
    public function addExcludeHiddenFrontendFilter()
    {
        return $this->addFieldToFilter('main_table.frontend_input', array('neq' => 'hidden'));
    }
}
