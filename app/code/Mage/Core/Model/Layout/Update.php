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
 * Layout Update model class
 *
 * @method int getIsTemporary() getIsTemporary()
 * @method int getLayoutLinkId() getLayoutLinkId()
 * @method string getUpdatedAt() getUpdatedAt()
 * @method string getXml() getXml()
 * @method Mage_Core_Model_Layout_Update setIsTemporary() setIsTemporary(int $isTemporary)
 * @method Mage_Core_Model_Layout_Update setHandle() setHandle(string $handle)
 * @method Mage_Core_Model_Layout_Update setXml() setXml(string $xml)
 * @method Mage_Core_Model_Layout_Update setStoreId() setStoreId(int $storeId)
 * @method Mage_Core_Model_Layout_Update setThemeId() setThemeId(int $themeId)
 * @method Mage_Core_Model_Layout_Update setUpdatedAt() setUpdatedAt(string $updateDateTime)
 * @method Mage_Core_Model_Resource_Layout_Update_Collection getCollection()
 */
class Mage_Core_Model_Layout_Update extends Mage_Core_Model_Abstract
{
    /**
     * Layout Update model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Layout_Update');
    }

    /**
     * Set current updated date
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->setUpdatedAt($this->getResource()->formatDate(time()));
        return parent::_beforeSave();
    }
}
