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
 * Enter description here ...
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
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
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
}
