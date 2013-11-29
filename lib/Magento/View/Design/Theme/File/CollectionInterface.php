<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Design\Theme\File;

/**
 * Design Theme File collection interface
 */
interface CollectionInterface
{
    /**
     * @return \Magento\View\Design\Theme\FileInterface[]
     */
    public function getItems();

    /**
     * Filter out files that do not belong to a theme
     *
     * @param \Magento\View\Design\ThemeInterface $theme
     * @return CollectionInterface
     */
    public function addThemeFilter(\Magento\View\Design\ThemeInterface $theme);

    /**
     * Set default order
     *
     * @param string $direction
     * @return CollectionInterface
     */
    public function setDefaultOrder($direction = 'ASC');

    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return CollectionInterface
     */
    public function addFieldToFilter($field, $condition = null);
}