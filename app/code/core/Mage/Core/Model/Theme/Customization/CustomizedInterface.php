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
 * Theme customized interface
 */
interface Mage_Core_Model_Theme_Customization_CustomizedInterface
{
    /**
     * Setter customization to customized theme
     *
     * @var Mage_Core_Model_Theme_Customization_CustomizationInterface $customization
     */
    public function setCustomization(Mage_Core_Model_Theme_Customization_CustomizationInterface $customization);

    /**
     * Return theme customization collection by type
     *
     * @param string $type
     */
    public function getCustomizationData($type);

    /**
     * Save theme customizations
     */
    public function saveThemeCustomization();

    /**
     * Check whether present customization objects
     */
    public function isCustomized();

    /**
     * Return path to customized theme files
     *
     * @return string|null
     */
    public function getCustomizationPath();
}
