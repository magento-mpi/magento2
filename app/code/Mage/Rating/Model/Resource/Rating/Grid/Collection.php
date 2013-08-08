<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating grid collection
 *
 * @category    Mage
 * @package     Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rating_Model_Resource_Rating_Grid_Collection extends Mage_Rating_Model_Resource_Rating_Collection
{
    /**
     * Add entity filter
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract|Mage_Rating_Model_Resource_Rating_Grid_Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addEntityFilter(Mage::registry('entityId'));
        return $this;
    }
}
