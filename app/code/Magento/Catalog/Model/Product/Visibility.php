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
 * Catalog Product visibilite model and attribute source model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product;

class Visibility extends \Magento\Object
{
    const VISIBILITY_NOT_VISIBLE = 1;

    const VISIBILITY_IN_CATALOG = 2;

    const VISIBILITY_IN_SEARCH = 3;

    const VISIBILITY_BOTH = 4;

    /**
     * Reference to the attribute instance
     *
     * @var \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    protected $_attribute;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Eav entity attribute
     *
     * @var \Magento\Eav\Model\Resource\Entity\Attribute
     */
    protected $_eavEntityAttribute;

    /**
     * Construct
     *
     * @param \Magento\Eav\Model\Resource\Entity\Attribute $eavEntityAttribute
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Eav\Model\Resource\Entity\Attribute $eavEntityAttribute,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        $this->_eavEntityAttribute = $eavEntityAttribute;
        $this->_coreData = $coreData;
        parent::__construct($data);
        $this->setIdFieldName('visibility_id');
    }

    /**
     * Retrieve visible in catalog ids array
     *
     * @return string[]
     */
    public function getVisibleInCatalogIds()
    {
        return array(self::VISIBILITY_IN_CATALOG, self::VISIBILITY_BOTH);
    }

    /**
     * Retrieve visible in search ids array
     *
     * @return string[]
     */
    public function getVisibleInSearchIds()
    {
        return array(self::VISIBILITY_IN_SEARCH, self::VISIBILITY_BOTH);
    }

    /**
     * Retrieve visible in site ids array
     *
     * @return string[]
     */
    public function getVisibleInSiteIds()
    {
        return array(self::VISIBILITY_IN_SEARCH, self::VISIBILITY_IN_CATALOG, self::VISIBILITY_BOTH);
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return array(
            self::VISIBILITY_NOT_VISIBLE => __('Not Visible Individually'),
            self::VISIBILITY_IN_CATALOG => __('Catalog'),
            self::VISIBILITY_IN_SEARCH => __('Search'),
            self::VISIBILITY_BOTH => __('Catalog, Search')
        );
    }

    /**
     * Retrieve all options
     *
     * @return array
     */
    public static function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value' => '', 'label' => ''));
        return $options;
    }

    /**
     * Retrieve all options
     *
     * @return array
     */
    public static function getAllOptions()
    {
        $res = array();
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array('value' => $index, 'label' => $value);
        }
        return $res;
    }

    /**
     * Retrieve option text
     *
     * @param int $optionId
     * @return string
     */
    public static function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array('unsigned' => true, 'default' => null, 'extra' => null);

        $column['type'] = \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT;
        $column['nullable'] = true;
        $column['comment'] = 'Catalog Product Visibility ' . $attributeCode . ' column';

        return array($attributeCode => $column);
    }

    /**
     * Retrieve Indexes for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        return array();
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->_eavEntityAttribute->getFlatUpdateSelect($this->getAttribute(), $store);
    }

    /**
     * Set attribute instance
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @return $this
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute instance
     *
     * @return \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * Add Value Sort To Collection Select
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @param string $dir direction
     * @return $this
     */
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeId = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();

        if ($this->getAttribute()->isScopeGlobal()) {
            $tableName = $attributeCode . '_t';
            $collection->getSelect()->joinLeft(
                array($tableName => $attributeTable),
                "e.entity_id={$tableName}.entity_id" .
                " AND {$tableName}.attribute_id='{$attributeId}'" .
                " AND {$tableName}.store_id='0'",
                array()
            );
            $valueExpr = $tableName . '.value';
        } else {
            $valueTable1 = $attributeCode . '_t1';
            $valueTable2 = $attributeCode . '_t2';
            $collection->getSelect()->joinLeft(
                array($valueTable1 => $attributeTable),
                "e.entity_id={$valueTable1}.entity_id" .
                " AND {$valueTable1}.attribute_id='{$attributeId}'" .
                " AND {$valueTable1}.store_id='0'",
                array()
            )->joinLeft(
                array($valueTable2 => $attributeTable),
                "e.entity_id={$valueTable2}.entity_id" .
                " AND {$valueTable2}.attribute_id='{$attributeId}'" .
                " AND {$valueTable2}.store_id='{$collection->getStoreId()}'",
                array()
            );
            $valueExpr = $collection->getConnection()->getCheckSql(
                $valueTable2 . '.value_id > 0',
                $valueTable2 . '.value',
                $valueTable1 . '.value'
            );
        }

        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }
}
