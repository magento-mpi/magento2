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
 * Theme customization interface
 */
interface Mage_Core_Model_Theme_Customization_CustomizationInterface
{
    /**
     * Return customization type
     */
    public function getType();

    /**
     * Setter for data for save
     *
     * @param mixed $data
     */
    public function setDataForSave($data);

    /**
     * Return collection customization form theme
     *
     * @param Mage_Core_Model_Theme_Customization_CustomizedInterface $theme
     */
    public function getCollectionByTheme(Mage_Core_Model_Theme_Customization_CustomizedInterface $theme);

    /**
     * Save data
     *
     * @param Mage_Core_Model_Theme_Customization_CustomizedInterface $theme
     */
    public function saveData(Mage_Core_Model_Theme_Customization_CustomizedInterface $theme);
}
