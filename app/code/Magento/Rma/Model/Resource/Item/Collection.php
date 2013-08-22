<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA entity collection
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Model_Resource_Item_Collection extends Magento_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('Magento_Rma_Model_Item', 'Magento_Rma_Model_Resource_Item');
    }

    /**
     * Add rma filter
     *
     * @param int $rmaEntityId
     * @return Magento_Rma_Model_Resource_Item_Collection
     */
    public function setOrderFilter($rmaEntityId)
    {
        $this->addAttributeToFilter('rma_entity_id', $rmaEntityId);
        return $this;

    }
}
