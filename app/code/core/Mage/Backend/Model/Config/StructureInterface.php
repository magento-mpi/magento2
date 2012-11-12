<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System configuration interface
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Backend_Model_Config_StructureInterface
{
    /**
     * Get all sections configuration
     *
     * @return array
     */
    public function getSections();

    /**
     * Get section configuration
     *
     * @param string $sectionCode
     * @param string $websiteCode
     * @param string $storeCode
     * @return array
     */
    public function getSection($sectionCode = null, $websiteCode = null, $storeCode = null);

    /**
     * Get all tabs configuration
     *
     * @return array
     */
    public function getTabs();

    /**
     * Get translate module name
     *
     * @param array $section
     * @param array $group
     * @param array $field
     * @return string
     */
    public function getAttributeModule($section = null, $group = null, $field = null);

    /**
     * Check whether node has child node that can be shown
     *
     * @param array $node
     * @param string $websiteCode
     * @param string $storeCode
     * @return boolean
     */
    public function hasChildren($node, $websiteCode = null, $storeCode = null);
}
