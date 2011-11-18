<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect Model Resource History
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_History extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor, setting table and index field
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('xmlconnect_history', 'history_id');
    }

    /**
     * Serialization for 'params' variable
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setParams(serialize($object->getParams()));
        return parent::_beforeSave($object);
    }

    /**
     * Deserialization for 'params' variable
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setParams(unserialize($object->getParams()));
        return parent::_afterLoad($object);
    }

    /**
     * Returns array of existing images
     *
     * @param int $id application instance Id
     * @return array
     */
    public function getLastParams($id)
    {
        $paramArray = array();
        $idFieldName = Mage::getModel('Mage_XmlConnect_Model_Application')->getIdFieldName();
        $select = $this->_getReadAdapter()->select()->from($this->getMainTable(), 'params')
            ->where($idFieldName . '=?', $id)->order(array('created_at ' . Zend_Db_Select::SQL_DESC));

        $params = $this->_getReadAdapter()->fetchOne($select);

        if (isset($params)) {
            $paramArray = unserialize($params);
        }
        return $paramArray;
    }
}
