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
 * Downloadable links purchased items resource collection
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Downloadable_Model_Link_Purchased_Item', 'Mage_Downloadable_Model_Resource_Link_Purchased_Item');
    }
}
