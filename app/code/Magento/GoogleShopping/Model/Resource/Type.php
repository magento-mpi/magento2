<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content Type resource model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Resource;

class Type extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('googleshopping_types', 'type_id');
    }

    /**
     * Return Type ID by Attribute Set Id and target country
     *
     * @param \Magento\GoogleShopping\Model\Type $model
     * @param int $attributeSetId Attribute Set
     * @param string $targetCountry Two-letters country ISO code
     * @return \Magento\GoogleShopping\Model\Type
     */
    public function loadByAttributeSetIdAndTargetCountry($model, $attributeSetId, $targetCountry)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('attribute_set_id=?', $attributeSetId)
            ->where('target_country=?', $targetCountry);

        $data = $this->_getReadAdapter()->fetchRow($select);
        $data = is_array($data) ? $data : array();
        $model->setData($data);
        return $model;
    }
}
