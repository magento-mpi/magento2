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
 * Eav attribute set model
 *
 * @method Magento_Eav_Model_Resource_Entity_Attribute_Set getResource()
 * @method int getEntityTypeId()
 * @method Magento_Eav_Model_Entity_Attribute_Set setEntityTypeId(int $value)
 * @method string getAttributeSetName()
 * @method Magento_Eav_Model_Entity_Attribute_Set setAttributeSetName(string $value)
 * @method int getSortOrder()
 * @method Magento_Eav_Model_Entity_Attribute_Set setSortOrder(int $value)
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Entity_Attribute_Set extends Magento_Core_Model_Abstract
{
    /**
     * Resource instance
     *
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Set
     */
    protected $_resource;

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'eav_entity_attribute_set';

    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @var Magento_Eav_Model_Entity_Attribute_GroupFactory
     */
    protected $_attrGroupFactory;

    /**
     * @var Magento_Eav_Model_Entity_AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * @var Magento_Eav_Model_Resource_Entity_Attribute
     */
    protected $_resourceAttribute;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Eav_Model_Entity_Attribute_GroupFactory $attrGroupFactory
     * @param Magento_Eav_Model_Entity_AttributeFactory $attributeFactory
     * @param Magento_Eav_Model_Resource_Entity_Attribute $resourceAttribute
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Eav_Model_Entity_Attribute_GroupFactory $attrGroupFactory,
        Magento_Eav_Model_Entity_AttributeFactory $attributeFactory,
        Magento_Eav_Model_Resource_Entity_Attribute $resourceAttribute,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_eavConfig = $eavConfig;
        $this->_attrGroupFactory = $attrGroupFactory;
        $this->_attributeFactory = $attributeFactory;
        $this->_resourceAttribute = $resourceAttribute;
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Eav_Model_Resource_Entity_Attribute_Set');
    }

    /**
     * Init attribute set from skeleton (another attribute set)
     *
     * @param int $skeletonId
     * @return Magento_Eav_Model_Entity_Attribute_Set
     */
    public function initFromSkeleton($skeletonId)
    {
        $groups = $this->_attrGroupFactory->create()
            ->getResourceCollection()
            ->setAttributeSetFilter($skeletonId)
            ->load();

        $newGroups = array();
        foreach ($groups as $group) {
            $newGroup = clone $group;
            $newGroup->setId(null)
                ->setAttributeSetId($this->getId())
                ->setDefaultId($group->getDefaultId());

            $groupAttributesCollection = $this->_attributeFactory->create()
                ->getResourceCollection()
                ->setAttributeGroupFilter($group->getId())
                ->load();

            $newAttributes = array();
            foreach ($groupAttributesCollection as $attribute) {
                $newAttribute = $this->_attributeFactory->create()
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
     * @return Magento_Eav_Model_Entity_Attribute_Set
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
            $attributeIds = $this->_resourceAttribute->getValidAttributeIds($ids);
        }
        if ($data['groups']) {
            foreach ($data['groups'] as $group) {
                $modelGroup = $this->_attrGroupFactory->create();
                $modelGroup->setId(is_numeric($group[0]) && $group[0] > 0 ? $group[0] : null)
                    ->setAttributeGroupName($group[1])
                    ->setAttributeSetId($this->getId())
                    ->setSortOrder($group[2]);

                if ($data['attributes']) {
                    foreach ($data['attributes'] as $attribute) {
                        if ($attribute[1] == $group[0] && in_array($attribute[0], $attributeIds)) {
                            $modelAttribute = $this->_attributeFactory->create();
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


        if ($data['not_attributes']) {
            $modelAttributeArray = array();
            foreach ($data['not_attributes'] as $attributeId) {
                $modelAttribute = $this->_attributeFactory->create();

                $modelAttribute->setEntityAttributeId($attributeId);
                $modelAttributeArray[] = $modelAttribute;
            }
            $this->setRemoveAttributes($modelAttributeArray);
        }

        if ($data['removeGroups']) {
            $modelGroupArray = array();
            foreach ($data['removeGroups'] as $groupId) {
                $modelGroup = $this->_attrGroupFactory->create();
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
     * @return bool
     * @throws Magento_Eav_Exception
     */
    public function validate()
    {
        $attributeSetName = $this->getAttributeSetName();
        if ($attributeSetName == '') {
            throw new Magento_Eav_Exception(
                __('Attribute set name is empty.')
            );
        }

        if (!$this->_getResource()->validate($this, $attributeSetName)) {
            throw new Magento_Eav_Exception(
                __('An attribute set with the "%1" name already exists.', $attributeSetName)
            );
        }

        return true;
    }

    /**
     * Add set info to attributes
     *
     * @param string|Magento_Eav_Model_Entity_Type $entityType
     * @param array $attributes
     * @param int $setId
     * @return Magento_Eav_Model_Entity_Attribute_Set
     */
    public function addSetInfo($entityType, array $attributes, $setId = null)
    {
        $attributeIds   = array();
        $entityType     = $this->_eavConfig->getEntityType($entityType);
        foreach ($attributes as $attribute) {
            $attribute = $this->_eavConfig->getAttribute($entityType, $attribute);
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
                $attribute = $this->_eavConfig->getAttribute($entityType, $attribute);
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
                    } else {
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

        return $setId ? $this->_getResource()->getDefaultGroupId($setId) : null;
    }

    /**
     * Get resource instance
     *
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _getResource()
    {
        return $this->_resource ?: parent::_getResource();
    }
}
