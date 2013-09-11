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
 * @method \Magento\Eav\Model\Resource\Entity\Attribute\Group _getResource()
 * @method \Magento\Eav\Model\Resource\Entity\Attribute\Group getResource()
 * @method int getAttributeSetId()
 * @method \Magento\Eav\Model\Entity\Attribute\Group setAttributeSetId(int $value)
 * @method string getAttributeGroupName()
 * @method \Magento\Eav\Model\Entity\Attribute\Group setAttributeGroupName(string $value)
 * @method int getSortOrder()
 * @method \Magento\Eav\Model\Entity\Attribute\Group setSortOrder(int $value)
 * @method int getDefaultId()
 * @method \Magento\Eav\Model\Entity\Attribute\Group setDefaultId(int $value)
 * @method string getAttributeGroupCode()
 * @method \Magento\Eav\Model\Entity\Attribute\Group setAttributeGroupCode(string $value)
 * @method string getTabGroupCode()
 * @method \Magento\Eav\Model\Entity\Attribute\Group setTabGroupCode(string $value)
 */
namespace Magento\Eav\Model\Entity\Attribute;

class Group extends \Magento\Core\Model\AbstractModel
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('\Magento\Eav\Model\Resource\Entity\Attribute\Group');
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
     * @return \Magento\Eav\Model\Entity\Attribute\Group
     */
    public function deleteGroups()
    {
        return $this->_getResource()->deleteGroups($this);
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Eav\Model\Entity\Attribute\Group
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
