<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Downloadable links purchased resource collection
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Model_Resource_Link_Purchased_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Downloadable_Model_Link_Purchased', 'Magento_Downloadable_Model_Resource_Link_Purchased');
    }

    /**
     * Add purchased items to collection
     *
     * @return Magento_Downloadable_Model_Resource_Link_Purchased_Collection
     */
    public function addPurchasedItemsToResult()
    {
        $this->getSelect()
            ->join(array('pi'=>$this->getTable('downloadable_link_purchased_item')),
                'pi.purchased_id=main_table.purchased_id');
        return $this;
    }
}
