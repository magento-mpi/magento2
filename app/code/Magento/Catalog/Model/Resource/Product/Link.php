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
 * Catalog product link resource model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Product;

class Link extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Product Link Attributes Table
     *
     * @var string
     */
    protected $_attributesTable;

    /**
     * Catalog product relation
     *
     * @var \Magento\Catalog\Model\Resource\Product\Relation
     */
    protected $_catalogProductRelation;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Catalog\Model\Resource\Product\Relation $catalogProductRelation
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Catalog\Model\Resource\Product\Relation $catalogProductRelation
    ) {
        $this->_catalogProductRelation = $catalogProductRelation;
        parent::__construct($resource);
    }

    /**
     * Define main table name and attributes table
     */
    protected function _construct()
    {
        $this->_init('catalog_product_link', 'link_id');
        $this->_attributesTable = $this->getTable('catalog_product_link_attribute');
    }

    /**
     * Save Product Links process
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $data
     * @param int $typeId
     * @return \Magento\Catalog\Model\Resource\Product\Link
     */
    public function saveProductLinks($product, $data, $typeId)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $attributes = $this->getAttributesByType($typeId);
        $adapter    = $this->_getWriteAdapter();

        $bind   = array(
            ':product_id'    => (int)$product->getId(),
            ':link_type_id'  => (int)$typeId
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), array('linked_product_id', 'link_id'))
            ->where('product_id = :product_id')
            ->where('link_type_id = :link_type_id');

        $links   = $adapter->fetchPairs($select, $bind);

        $deleteIds = array();
        foreach ($links as $linkedProductId => $linkId) {
            if (!isset($data[$linkedProductId])) {
                $deleteIds[] = (int)$linkId;
            }
        }
        if (!empty($deleteIds)) {
            $adapter->delete($this->getMainTable(), array(
                'link_id IN (?)' => $deleteIds,
            ));
        }

        foreach ($data as $linkedProductId => $linkInfo) {
            $linkId = null;
            if (isset($links[$linkedProductId])) {
                $linkId = $links[$linkedProductId];
                unset($links[$linkedProductId]);
            } else {
                $bind = array(
                    'product_id'        => $product->getId(),
                    'linked_product_id' => $linkedProductId,
                    'link_type_id'      => $typeId
                );
                $adapter->insert($this->getMainTable(), $bind);
                $linkId = $adapter->lastInsertId($this->getMainTable());
            }

            foreach ($attributes as $attributeInfo) {
                $attributeTable = $this->getAttributeTypeTable($attributeInfo['type']);
                if ($attributeTable) {
                    if (isset($linkInfo[$attributeInfo['code']])) {
                        $value = $this->_prepareAttributeValue($attributeInfo['type'],
                            $linkInfo[$attributeInfo['code']]);
                        $bind = array(
                            'product_link_attribute_id' => $attributeInfo['id'],
                            'link_id'                   => $linkId,
                            'value'                     => $value
                        );
                        $adapter->insertOnDuplicate($attributeTable, $bind, array('value'));
                    } else {
                        $adapter->delete($attributeTable, array(
                            'link_id = ?'                   => $linkId,
                            'product_link_attribute_id = ?' => $attributeInfo['id']
                        ));
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Prepare link attribute value by attribute type
     *
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function _prepareAttributeValue($type, $value)
    {
        if ($type == 'int') {
            $value = (int)$value;
        } elseif ($type == 'decimal') {
            $value = (float)sprintf('%F', $value);
        }
        return $value;
    }

    /**
     * Retrieve product link attributes by link type
     *
     * @param int $typeId
     * @return array
     */
    public function getAttributesByType($typeId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_attributesTable, array(
                'id'    => 'product_link_attribute_id',
                'code'  => 'product_link_attribute_code',
                'type'  => 'data_type'
            ))
            ->where('link_type_id = ?', $typeId);
        return $adapter->fetchAll($select);
    }

    /**
     * Returns table for link attribute by attribute type
     *
     * @param string $type
     * @return string
     */
    public function getAttributeTypeTable($type)
    {
        return $this->getTable('catalog_product_link_attribute_' . $type);
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @param int $typeId
     * @return array
     */
    public function getChildrenIds($parentId, $typeId)
    {
        $adapter     = $this->_getReadAdapter();
        $childrenIds = array();
        $bind        = array(
            ':product_id'    => (int)$parentId,
            ':link_type_id'  => (int)$typeId
        );
        $select = $adapter->select()
            ->from(array('l' => $this->getMainTable()), array('linked_product_id'))
            ->where('product_id = :product_id')
            ->where('link_type_id = :link_type_id');

        $childrenIds[$typeId] = array();
        $result = $adapter->fetchAll($select, $bind);
        foreach ($result as $row) {
            $childrenIds[$typeId][$row['linked_product_id']] = $row['linked_product_id'];
        }

        return $childrenIds;
    }

    /**
     * Retrieve parent ids array by required child
     *
     * @param int|array $childId
     * @param int $typeId
     * @return array
     */
    public function getParentIdsByChild($childId, $typeId)
    {
        $parentIds  = array();
        $adapter    = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('product_id', 'linked_product_id'))
            ->where('linked_product_id IN(?)', $childId)
            ->where('link_type_id = ?', $typeId);

        $result = $adapter->fetchAll($select);
        foreach ($result as $row) {
            $parentIds[] = $row['product_id'];
        }

        return $parentIds;
    }
}
