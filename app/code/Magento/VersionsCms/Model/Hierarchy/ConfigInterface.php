<?php
/**
 * CMS menu hierarchy configuration model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Hierarchy;

interface ConfigInterface
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
     * @return \Magento\Object|boolean
     */
    public function getContextMenuLayout($layoutName);
}
