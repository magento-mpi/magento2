<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\Resource\Link\Purchased;

/**
 * Downloadable links purchased resource collection
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Downloadable\Model\Link\Purchased', 'Magento\Downloadable\Model\Resource\Link\Purchased');
    }

    /**
     * Add purchased items to collection
     *
     * @return $this
     */
    public function addPurchasedItemsToResult()
    {
        $this->getSelect()
            ->join(array('pi'=>$this->getTable('downloadable_link_purchased_item')),
                'pi.purchased_id=main_table.purchased_id');
        return $this;
    }
}
