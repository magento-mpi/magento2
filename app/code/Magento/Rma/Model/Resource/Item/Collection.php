<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model\Resource\Item;

/**
 * RMA entity collection
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Eav\Model\Entity\Collection\AbstractCollection
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Rma\Model\Item', 'Magento\Rma\Model\Resource\Item');
    }

    /**
     * Add rma filter
     *
     * @param int $rmaEntityId
     * @return $this
     */
    public function setOrderFilter($rmaEntityId)
    {
        $this->addAttributeToFilter('rma_entity_id', $rmaEntityId);
        return $this;

    }
}
