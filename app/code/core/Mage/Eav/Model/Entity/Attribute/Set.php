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
 * Eav attribute set model
 *
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Set _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Set getResource()
 * @method int getEntityTypeId()
 * @method Mage_Eav_Model_Entity_Attribute_Set setEntityTypeId(int $value)
 * @method string getAttributeSetName()
 * @method Mage_Eav_Model_Entity_Attribute_Set setAttributeSetName(string $value)
 * @method int getSortOrder()
 * @method Mage_Eav_Model_Entity_Attribute_Set setSortOrder(int $value)
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Attribute_Set extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'eav_entity_attribute_set';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Eav_Model_Resource_Entity_Attribute_Set');
    }

    /**
     * Init attribute set from skeleton (another attribute set)
     *
     * @param int $skeletonId
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    public function initFromSkeleton($skeletonId)
    {
        $groups = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Group')
            ->getResourceCollection()
            ->setAttributeSetFilter($skeletonId)
            ->load();

        $newGroups = array();
        foreach ($groups as $group) {
            $newGroup = clone $group;
            $newGroup->setId(null)
                ->setAttributeSetId($this->getId())
                ->setDefaultId($group->getDefaultId());

            $groupAttributesCollection = Mage::getModel('Mage_Eav_Model_Entity_Attribute')
                ->getResourceCollection()
                ->setAttributeGroupFilter($group->getId())
                ->load();

            $newAttributes = array();
            foreach ($groupAttributesCollection as $attribute) {
                $newAttribute = Mage::getModel('Mage_Eav_Model_Entity_Attribute')
                    ->setId($attribute->getId())
                    //->setAttributeGroupId($newGroup->getId())
                    ->setAttributeSetId($this->getId())
                    ->setEntityTypeId($this->getEntityTypeId())
                    ->setSortOrder($attribute->getSortOrder());
                $newAttributes[] = $newAttribute;
            }
            $newGroup->setAttributes($newAttributes);
            $newGroups[] = $newGroup;
        }
        $this->setGroups($newGroups);

        return $this;
    }

    /**
     * Collect data for save
     *
     * @param array $data
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    public function organizeData($data)
    {
        $modelGroupArray = array();
        $modelAttributeArray = array();
        $attributeIds = array();
        if ($data['attributes']) {
            $ids = array();
            foreach ($data['attributes'] as $attribute) {
                $ids[] = $attribute[0];
            }
            $attributeIds = Mage::getResourceSingleton('Mage_Eav_Model_Resource_Entity_Attribute')
                ->getValidAttributeIds($ids);
        }
        if( $data['groups'] ) {
            foreach ($data['groups'] as $group) {
                $modelGroup = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Group');
                $modelGroup->setId(is_numeric($group[0]) && $group[0] > 0 ? $group[0] : null)
                    ->setAttributeGroupName($group[1])
                    ->setAttributeSetId($this->getId())
                    ->setSortOrder($group[2]);

                if( $data['attributes'] ) {
                    foreach( $data['attributes'] as $attribute ) {
                        if( $attribute[1] == $group[0] && in_array($attribute[0], $attributeIds) ) {
                            $modelAttribute = Mage::getModel('Mage_Eav_Model_Entity_Attribute');
                            $modelAttribute->setId($attribute[0])
                                ->setAttributeGroupId($attribute[1])
                                ->setAttributeSetId($this->getId())
                                ->setEntityTypeId($this->getEntityTypeId())
                                ->setSortOrder($attribute[2]);
                            $modelAttributeArray[] = $modelAttribute;
                        }
                    }
                    $modelGroup->setAttributes($modelAttributeArray);
                    $modelAttributeArray = array();
                }
                $modelGroupArray[] = $modelGroup;
            }
            $this->setGroups($modelGroupArray);
        }


        if( $data['not_attributes'] ) {
            $modelAttributeArray = array();
            foreach( $data['not_attributes'] as $attributeId ) {
                $modelAttribute = Mage::getModel('Mage_Eav_Model_Entity_Attribute');

                $modelAttribute->setEntityAttributeId($attributeId);
                $modelAttributeArray[] = $modelAttribute;
            }
            $this->setRemoveAttributes($modelAttributeArray);
        }

        if( $data['removeGroups'] ) {
            $modelGroupArray = array();
            foreach( $data['removeGroups'] as $groupId ) {
                $modelGroup = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Group');
                $modelGroup->setId($groupId);

                $modelGroupArray[] = $modelGroup;
            }
            $this->setRemoveGroups($modelGroupArray);
        }
        $this->setAttributeSetName($data['attribute_set_name'])
            ->setEntityTypeId($this->getEntityTypeId());

        return $this;
    }

    /**
     * Validate attribute set name
     *
     * @param string $name
     * @throws Mage_Eav_Exception
     * @return bool
     */
    public function validate()
    {
        if (!$this->_getResource()->validate($this, $this->getAttributeSetName())) {
            throw Mage::exception('Mage_Eav',
                Mage::helper('Mage_Eav_Helper_Data')->__('Attribute set with the "%s" name already exists.', $this->getAttributeSetName())
            );
        }

        return true;
    }

    /**
     * Add set info to attributes
     *
     * @param string|Mage_Eav_Model_Entity_Type $entityType
     * @param array $attributes
     * @param int $setId
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    public function addSetInfo($entityType, array $attributes, $setId = null)
    {
        $attributeIds   = array();
        $config         = Mage::getSingleton('Mage_Eav_Model_Config');
        $entityType     = $config->getEntityType($entityType);
        foreach ($attributes as $attribute) {
            $attribute = $config->getAttribute($entityType, $attribute);
            if ($setId && is_array($attribute->getAttributeSetInfo($setId))) {
                continue;
            }
            if (!$attribute->getAttributeId()) {
                continue;
            }
            $attributeIds[] = $attribute->getAttributeId();
        }

        if ($attributeIds) {
            $setInfo = $this->_getResource()
                ->getSetInfo($attributeIds, $setId);

            foreach ($attributes as $attribute) {
                $attribute = $config->getAttribute($entityType, $attribute);
                if (!$attribute->getAttributeId()) {
                    continue;
                }
                if (!in_array($attribute->getAttributeId(), $attributeIds)) {
                    continue;
                }
                if (is_numeric($setId)) {
                    $attributeSetInfo = $attribute->getAttributeSetInfo();
                    if (!is_array($attributeSetInfo)) {
                        $attributeSetInfo = array();
                    }
                    if (isset($setInfo[$attribute->getAttributeId()][$setId])) {
                        $attributeSetInfo[$setId] = $setInfo[$attribute->getAttributeId()][$setId];
                    }
                    $attribute->setAttributeSetInfo($attributeSetInfo);
                } else {
                    if (isset($setInfo[$attribute->getAttributeId()])) {
                        $attribute->setAttributeSetInfo($setInfo[$attribute->getAttributeId()]);
                    }
                    else {
                        $attribute->setAttributeSetInfo(array());
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Return default Group Id for current or defined Attribute Set
     *
     * @param int $setId
     * @return int|null
     */
    public function getDefaultGroupId($setId = null)
    {
        if ($setId === null) {
            $setId = $this->getId();
        }
        if ($setId) {
            $groupId = $this->_getResource()->getDefaultGroupId($setId);
        } else {
            $groupId = null;
        }
        return $groupId;
    }
}
