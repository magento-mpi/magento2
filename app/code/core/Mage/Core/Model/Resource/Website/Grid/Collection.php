<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid collection
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Website_Grid_Collection extends Mage_Core_Model_Resource_Website_Collection
{
    /**
     * Join website and store names
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|Mage_Core_Model_Resource_Website_Grid_Collection
     */
    protected function  _initSelect()
    {
        parent::_initSelect();
        $this->joinGroupAndStore();
        return $this;
    }
}
