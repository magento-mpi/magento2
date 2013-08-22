<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method Magento_Eav_Model_Resource_Entity_Attribute_Group _getResource()
 * @method Magento_Eav_Model_Resource_Entity_Attribute_Group getResource()
 * @method int getAttributeSetId()
 * @method Magento_Eav_Model_Entity_Attribute_Group setAttributeSetId(int $value)
 * @method string getAttributeGroupName()
 * @method Magento_Eav_Model_Entity_Attribute_Group setAttributeGroupName(string $value)
 * @method int getSortOrder()
 * @method Magento_Eav_Model_Entity_Attribute_Group setSortOrder(int $value)
 * @method int getDefaultId()
 * @method Magento_Eav_Model_Entity_Attribute_Group setDefaultId(int $value)
 * @method string getAttributeGroupCode()
 * @method Magento_Eav_Model_Entity_Attribute_Group setAttributeGroupCode(string $value)
 * @method string getTabGroupCode()
 * @method Magento_Eav_Model_Entity_Attribute_Group setTabGroupCode(string $value)
 */
class Magento_Eav_Model_Entity_Attribute_Group extends Magento_Core_Model_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Eav_Model_Resource_Entity_Attribute_Group');
    }

    /**
     * Checks if current attribute group exists
     *
     * @return boolean
     */
    public function itemExists()
    {
        return $this->_getResource()->itemExists($this);
    }

    /**
     * Delete groups
     *
     * @return Magento_Eav_Model_Entity_Attribute_Group
     */
    public function deleteGroups()
    {
        return $this->_getResource()->deleteGroups($this);
    }

    /**
     * Processing object before save data
     *
     * @return Magento_Eav_Model_Entity_Attribute_Group
     */
    protected function _beforeSave()
    {
        if (!$this->getAttributeGroupCode()) {
            $groupName = $this->getAttributeGroupName();
            if ($groupName) {
                $this->setAttributeGroupCode(trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($groupName)), '-'));
            }
        }
        return parent::_beforeSave();
    }
}
