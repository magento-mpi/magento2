<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Model\Attribute;

use Magento\Catalog\Model\Attribute\LockValidatorInterface;

class LockValidator implements LockValidatorInterface
{
    /**
     * @var \Magento\App\Resource
     */
    protected $resorce;

    /**
     * Check attribute lock state
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param null $attributeSet
     * @throws \Magento\Core\Exception
     *
     * @return void
     */
    public function validate(\Magento\Core\Model\AbstractModel $object, $attributeSet = null)
    {
        $adapter = $this->resorce->getConnection('read');
        $attrTable    = $this->resorce->getTableName('catalog_product_super_attribute');
        $productTable = $this->resorce->getTableName('catalog_product_entity');

        $bind = array('attribute_id' => $object->getAttributeId());
        $select = clone $adapter->select();
        $select->reset()
            ->from(array('main_table' => $attrTable), array('psa_count' => 'COUNT(product_super_attribute_id)'))
            ->join(array('entity' => $productTable), 'main_table.product_id = entity.entity_id')
            ->where('main_table.attribute_id = :attribute_id')
            ->group('main_table.attribute_id')
            ->limit(1);

        if ($attributeSet !== null) {
            $bind['attribute_set_id'] = $attributeSet;
            $select->where('entity.attribute_set_id = :attribute_set_id');
        }

        if ($adapter->fetchOne($select, $bind)) {
            throw new \Magento\Core\Exception(
                __('This attribute is used in configurable products.')
            );
        }
    }
} 
