<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

interface ViewInterface
{
    /**
     * Load layout updates
     *
     * @return ViewInterface
     */
    public function loadLayoutUpdates();

    /**
     * Rendering layout
     *
     * @param   string $output
     * @return  ViewInterface
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
     * @return  ViewInterface
     * @throws  \RuntimeException
     */
    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true);

    /**
     * Generate layout xml
     *
     * @return ViewInterface
     */
    public function generateLayoutXml();

    /**
     * Add layout updates handles associated with the action page
     *
     * @param array $parameters page parameters
     * @param string $defaultHandle
     * @return bool
     */
    public function addPageLayoutHandles(array $parameters = array(), $defaultHandle = null);

    /**
     * Generate layout blocks
     *
     * @return ViewInterface
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
     * @return ViewInterface
     */
    public function addActionLayoutHandles();

    /**
     * Set isLayoutLoaded flag
     *
     * @param bool $value
     * @return void
     */
    public function setIsLayoutLoaded($value);

}
