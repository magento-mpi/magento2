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
 * Layout update resource model
 */
class Mage_Core_Model_Resource_Layout_Update extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('core_layout_update', 'layout_update_id');
    }

    /**
     * Retrieve layout updates by handle
     *
     * @param string $handle
     * @param array $params
     * @return string
     */
    public function fetchUpdatesByHandle($handle, $params = array())
    {
        $bind = array(
            'store_id' => Mage::app()->getStore()->getId(),
            'theme_id' => Mage::getDesign()->getDesignTheme()->getThemeId(),
        );

        foreach ($params as $key => $value) {
            if (isset($bind[$key])) {
                $bind[$key] = $value;
            }
        }
        $bind['layout_update_handle'] = $handle;
        $result = '';

        $readAdapter = $this->_getReadAdapter();
        if ($readAdapter) {
            $select = $readAdapter->select()
                ->from(array('layout_update' => $this->getMainTable()), array('xml'))
                ->join(array('link'=>$this->getTable('core_layout_link')),
                    'link.layout_update_id=layout_update.layout_update_id', '')
                ->where('link.store_id IN (0, :store_id)')
                ->where('link.theme_id = :theme_id')
                ->where('layout_update.handle = :layout_update_handle')
                ->order('layout_update.sort_order ' . Varien_Db_Select::SQL_ASC);

            $result = join('', $readAdapter->fetchCol($select, $bind));
        }
        return $result;
    }

    /**
     * Update a "layout update link" if relevant data is provided
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Layout_Update
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $data = $object->getData();
        if (isset($data['store_id']) && isset($data['theme_id'])) {
            $this->_getWriteAdapter()->insertOnDuplicate($this->getTable('core_layout_link'), array(
                'store_id'         => $data['store_id'],
                'theme_id'         => $data['theme_id'],
                'layout_update_id' => $object->getId(),
            ));
        }
        Mage::app()->cleanCache(array('layout', Mage_Core_Model_Layout_Merge::LAYOUT_GENERAL_CACHE_TAG));
        return parent::_afterSave($object);
    }
}
