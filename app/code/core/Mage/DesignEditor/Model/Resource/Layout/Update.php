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
 * VDE area layout update resource model
 */
class Mage_DesignEditor_Model_Resource_Layout_Update extends Mage_Core_Model_Resource_Layout_Update
{
    /**
     * Get select to fetch updates by handle
     *
     * @param bool $loadAllUpdates
     * @return Varien_Db_Select
     */
    protected function _getFetchUpdatesByHandleSelect($loadAllUpdates = false)
    {
        // always load all layout updates in vde mode
        $loadAllUpdates = true;
        return parent::_getFetchUpdatesByHandleSelect($loadAllUpdates);
    }

    /**
     * Make temporary updates for given theme and given stores permanent
     *
     * @param int $themeId
     * @param array $storeIds
     */
    public function makeTemporaryLayoutUpdatesPermanent($themeId, array $storeIds)
    {
        $this->_getWriteAdapter()->update($this->getTable('core_layout_link'),
            array('is_temporary' => 0),
            array(
                'theme_id = ?'   => $themeId,
                'store_id IN(?)' => $storeIds,
            )
        );
    }
}
