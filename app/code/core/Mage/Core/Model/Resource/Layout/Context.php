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
 * Layout context resource model
 */
class Mage_Core_Model_Resource_Layout_Context extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('core_layout_update_context', 'context_id');
    }
}
