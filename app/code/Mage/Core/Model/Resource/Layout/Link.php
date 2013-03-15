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
 * Layout Link resource model
 */
class Mage_Core_Model_Resource_Layout_Link extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('core_layout_link', 'layout_link_id');
    }
}
