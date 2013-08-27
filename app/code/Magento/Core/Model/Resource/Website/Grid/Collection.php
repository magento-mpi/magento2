<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Resource_Website_Grid_Collection extends Magento_Core_Model_Resource_Website_Collection
{
    /**
     * Join website and store names
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract|Magento_Core_Model_Resource_Website_Grid_Collection
     */
    protected function  _initSelect()
    {
        parent::_initSelect();
        $this->joinGroupAndStore();
        return $this;
    }
}
