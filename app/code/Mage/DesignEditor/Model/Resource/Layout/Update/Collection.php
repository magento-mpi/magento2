<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * VDE Layout update collection model
 */
class Mage_DesignEditor_Model_Resource_Layout_Update_Collection
    extends Mage_Core_Model_Resource_Layout_Update_Collection
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mage_DesignEditor_Model_Layout_Update', 'Mage_DesignEditor_Model_Resource_Layout_Update');
    }
}
