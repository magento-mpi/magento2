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
namespace Magento\Rma\Model\Resource\Item;

class Collection extends \Magento\Eav\Model\Entity\Collection\AbstractCollection
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('Magento\Rma\Model\Item', 'Magento\Rma\Model\Resource\Item');
    }

    /**
     * Add rma filter
     *
     * @param int $rmaEntityId
     * @return \Magento\Rma\Model\Resource\Item\Collection
     */
    public function setOrderFilter($rmaEntityId)
    {
        $this->addAttributeToFilter('rma_entity_id', $rmaEntityId);
        return $this;

    }
}
