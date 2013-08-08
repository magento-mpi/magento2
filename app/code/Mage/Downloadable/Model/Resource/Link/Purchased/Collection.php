<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Downloadable links purchased resource collection
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Resource_Link_Purchased_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Downloadable_Model_Link_Purchased', 'Mage_Downloadable_Model_Resource_Link_Purchased');
    }

    /**
     * Add purchased items to collection
     *
     * @return Mage_Downloadable_Model_Resource_Link_Purchased_Collection
     */
    public function addPurchasedItemsToResult()
    {
        $this->getSelect()
            ->join(array('pi'=>$this->getTable('downloadable_link_purchased_item')),
                'pi.purchased_id=main_table.purchased_id');
        return $this;
    }
}
