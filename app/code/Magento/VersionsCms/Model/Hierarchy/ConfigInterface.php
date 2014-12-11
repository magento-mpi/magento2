<?php
/**
 * CMS menu hierarchy configuration model interface
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
     * @return \Magento\Framework\Object|bool
     */
    public function getContextMenuLayout($layoutName);
}
