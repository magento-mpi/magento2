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
 * List of page assets that maintains association of properties with a uniquely identified page type.
 * Allows to combine page types with the same properties into groups.
 */
class Mage_Page_Model_GroupedAssets
{
    /**#@+
     * Special properties, enforced to be grouped by
     */
    const PROPERTY_CONTENT_TYPE = 'content_type';
    const PROPERTY_CAN_MERGE    = 'can_merge';
    /**#@-*/

    /**
     * @var Mage_Core_Model_Page_Asset_Collection
     */
    private $_assets;

    /**
     * @var array
     */
    private $_properties = array();

    /**
     * @param Mage_Core_Model_Page $page
     */
    public function __construct(Mage_Core_Model_Page $page)
    {
        $this->_assets = $page->getAssets();
    }

    /**
     * Add asset with optional properties
     *
     * @param string $identifier
     * @param Mage_Core_Model_Page_Asset_AssetInterface $asset
     * @param array $properties
     */
    public function addAsset($identifier, Mage_Core_Model_Page_Asset_AssetInterface $asset, array $properties = array())
    {
        $this->_assets->add($identifier, $asset);
        $this->_properties[$identifier] = $properties;
    }

    /**
     * Remove asset instance by identifier
     *
     * @param $identifier
     */
    public function removeAsset($identifier)
    {
        $this->_assets->remove($identifier);
        unset($this->_properties[$identifier]);
    }

    /**
     * Retrieved assets, grouping ones that have the same properties
     *
     * @return Mage_Page_Model_Asset_PropertyGroup[]
     */
    public function groupByProperties()
    {
        $result = array();
        /** @var $asset Mage_Core_Model_Page_Asset_AssetInterface */
        foreach ($this->_assets->getAll() as $assetId => $asset) {
            $properties = isset($this->_properties[$assetId]) ? $this->_properties[$assetId] : array();
            $properties[self::PROPERTY_CONTENT_TYPE] = $asset->getContentType();
            $properties[self::PROPERTY_CAN_MERGE] = $asset instanceof Mage_Core_Model_Page_Asset_MergeableInterface;
            $groupId = serialize($properties);
            if (isset($result[$groupId])) {
                /** @var $group Mage_Page_Model_Asset_PropertyGroup */
                $group = $result[$groupId];
            } else {
                $group = new Mage_Page_Model_Asset_PropertyGroup($properties);
                $result[$groupId] = $group;
            }
            $group->add($assetId, $asset);
        }
        return $result;
    }
}
