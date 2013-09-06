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
 * Layout Link resource model
 */
class Magento_Core_Model_Resource_Layout_Link extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('core_layout_link', 'layout_link_id');
    }
}
