<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Group _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Group getResource()
 * @method int getAttributeSetId()
 * @method Mage_Eav_Model_Entity_Attribute_Group setAttributeSetId(int $value)
 * @method string getAttributeGroupName()
 * @method Mage_Eav_Model_Entity_Attribute_Group setAttributeGroupName(string $value)
 * @method int getSortOrder()
 * @method Mage_Eav_Model_Entity_Attribute_Group setSortOrder(int $value)
 * @method int getDefaultId()
 * @method Mage_Eav_Model_Entity_Attribute_Group setDefaultId(int $value)
 * @method string getAttributeGroupCode()
 * @method Mage_Eav_Model_Entity_Attribute_Group setAttributeGroupCode(string $value)
 * @method string getTabGroupCode()
 * @method Mage_Eav_Model_Entity_Attribute_Group setTabGroupCode(string $value)
 */
class Mage_Eav_Model_Entity_Attribute_Group extends Mage_Core_Model_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Eav_Model_Resource_Entity_Attribute_Group');
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
     * @return Mage_Eav_Model_Entity_Attribute_Group
     */
    public function deleteGroups()
    {
        return $this->_getResource()->deleteGroups($this);
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Eav_Model_Entity_Attribute_Group
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
