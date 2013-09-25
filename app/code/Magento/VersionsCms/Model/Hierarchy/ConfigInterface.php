<?php
/**
 * CMS menu hierarchy configuration model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_VersionsCms_Model_Hierarchy_ConfigInterface
{
    /**
     * Return available Context Menu layouts output
     *
     * @return array
     */
    public function getAllMenuLayouts();

    /**
     * Return Context Menu layout by its name
     *
     * @param string $layoutName
     * @return Magento_Object|boolean
     */
    public function getContextMenuLayout($layoutName);
}
