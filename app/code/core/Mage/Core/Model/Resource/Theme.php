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
 * Theme resource model
 */
class Mage_Core_Model_Resource_Theme extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('core_theme', 'theme_id');
    }

    /**
     * Load an object
     *
     * @param Mage_Core_Model_Theme $object
     * @param string $tempId
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    public function loadByTempId($object, $tempId)
    {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($tempId)) {
            list($area, $themePath) = explode('/', $tempId, 2);
            $select = $read->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable() . '.area=?', $area)
                ->where($this->getMainTable() . '.theme_path=?', $themePath);

            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;
    }
}
