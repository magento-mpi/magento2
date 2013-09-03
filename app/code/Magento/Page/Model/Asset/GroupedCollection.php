<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * List of page assets that combines into groups ones having the same properties
 */
class Magento_Page_Model_Asset_GroupedCollection extends Magento_Core_Model_Page_Asset_Collection
{
    /**#@+
     * Special properties, enforced to be grouped by
     */
    const PROPERTY_CONTENT_TYPE = 'content_type';
    const PROPERTY_CAN_MERGE    = 'can_merge';
    /**#@-*/

    /**
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @var Magento_Page_Model_Asset_PropertyGroup[]
     */
    private $_groups = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Add an instance, identified by a unique identifier, to the list and to the corresponding group
     *
     * @param string $identifier
     * @param Magento_Core_Model_Page_Asset_AssetInterface $asset
     * @param array $properties
     */
    public function add($identifier, Magento_Core_Model_Page_Asset_AssetInterface $asset, array $properties = array())
    {
        parent::add($identifier, $asset);
        $properties[self::PROPERTY_CONTENT_TYPE] = $asset->getContentType();
        $properties[self::PROPERTY_CAN_MERGE] = $asset instanceof Magento_Core_Model_Page_Asset_MergeableInterface;
        $this->_getGroupFor($properties)->add($identifier, $asset);
    }

    /**
     * Retrieve existing or new group matching the properties
     *
     * @param array $properties
     * @return Magento_Page_Model_Asset_PropertyGroup
     */
    private function _getGroupFor(array $properties)
    {
        /** @var $existingGroup Magento_Page_Model_Asset_PropertyGroup */
        foreach ($this->_groups as $existingGroup) {
            if ($existingGroup->getProperties() == $properties) {
                return $existingGroup;
            }
        }
        /** @var $newGroup Magento_Page_Model_Asset_PropertyGroup */
        $newGroup = $this->_objectManager->create(
            'Magento_Page_Model_Asset_PropertyGroup', array('properties' => $properties)
        );
        $this->_groups[] = $newGroup;
        return $newGroup;
    }

    /**
     * Remove an instance from the list and from the corresponding group
     *
     * @param string $identifier
     */
    public function remove($identifier)
    {
        parent::remove($identifier);
        /** @var $group Magento_Page_Model_Asset_PropertyGroup */
        foreach ($this->_groups as $group) {
            if ($group->has($identifier)) {
                $group->remove($identifier);
                return;
            }
        }
    }

    /**
     * Retrieve groups, containing assets that have the same properties
     *
     * @return Magento_Page_Model_Asset_PropertyGroup[]
     */
    public function getGroups()
    {
        return $this->_groups;
    }
}
