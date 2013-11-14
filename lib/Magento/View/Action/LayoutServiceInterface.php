<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Action;

interface LayoutServiceInterface
{
    /**
     * Load layout updates
     *
     * @return LayoutServiceInterface
     */
    public function loadLayoutUpdates();

    /**
     * Rendering layout
     *
     * @param   string $output
     * @return  LayoutServiceInterface
     */
    public function renderLayout($output = '');

    /**
     * Retrieve the default layout handle name for the current action
     *
     * @return string
     */
    public function getDefaultLayoutHandle();

    /**
     * Load layout by handles(s)
     *
     * @param   string|null|bool $handles
     * @param   bool $generateBlocks
     * @param   bool $generateXml
     * @return  LayoutServiceInterface
     * @throws  \RuntimeException
     */
    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true);

    /**
     * Generate layout xml
     *
     * @return LayoutServiceInterface
     */
    public function generateLayoutXml();

    /**
     * Add layout updates handles associated with the action page
     *
     * @param array $parameters page parameters
     * @return bool
     */
    public function addPageLayoutHandles(array $parameters = array());

    /**
     * Generate layout blocks
     *
     * @return LayoutServiceInterface
     */
    public function generateLayoutBlocks();

    /**
     * Retrieve current layout object
     *
     * @return \Magento\View\LayoutInterface
     */
    public function getLayout();

    /**
     * Add layout handle by full controller action name
     *
     * @return LayoutServiceInterface
     */
    public function addActionLayoutHandles();

    /**
     * Set isLayoutLoaded flag
     *
     * @param bool $value
     */
    public function setIsLayoutLoaded($value);

}