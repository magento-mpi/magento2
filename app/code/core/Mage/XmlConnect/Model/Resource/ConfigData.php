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
 * Configuration data recourse model
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_ConfigData extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize configuration data
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('xmlconnect_config_data', null);
    }

    /**
     * Save config value
     *
     * @param int $applicationId
     * @param string $category
     * @param string $path
     * @param string $value
     * @return Mage_XmlConnect_Model_Resource_ConfigData
     */
    public function saveConfig($applicationId, $category, $path, $value)
    {
        $newData = array(
            'application_id' => $applicationId,
            'category'  => $category,
            'path'      => $path,
            'value'     => $value
        );

        $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), $newData, array('value'));
        return $this;
    }

    /**
     * Delete config value
     *
     * @param int $applicationId
     * @param bool $category
     * @param bool $path
     * @param bool $pathLike
     * @return Mage_XmlConnect_Model_Resource_ConfigData
     */
    public function deleteConfig($applicationId, $category = false, $path = false, $pathLike = true)
    {
        try {
            $this->_getWriteAdapter()->beginTransaction();
            $writeAdapter = $this->_getWriteAdapter();
            $deleteWhere[] = $writeAdapter->quoteInto('application_id=?', $applicationId);
            if ($category) {
                $deleteWhere[] = $writeAdapter->quoteInto('category=?', $category);
            }
            if ($path) {
                $deleteWhere[] = $pathLike ? $writeAdapter->quoteInto('path like ?', $path . '/%')
                    : $writeAdapter->quoteInto('path=?', $path);
            }
            $writeAdapter->delete($this->getMainTable(), $deleteWhere);
            $this->_getWriteAdapter()->commit();
        } catch (Mage_Core_Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        } catch (Exception $e){
            $this->_getWriteAdapter()->rollBack();
            Mage::logException($e);
        }

        return $this;
    }
}
