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
     * @return \Magento\View\Action\LayoutServiceInterface
     */
    public function loadLayoutUpdates();

    /**
     * Rendering layout
     *
     * @param   string $output
     * @return  \Magento\View\Action\LayoutServiceInterface
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
     * @return  $this
     * @throws  \RuntimeException
     */
    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true);

    /**
     * Generate layout xml
     *
     * @return \Magento\View\Action\LayoutServiceInterface
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
     * @return \Magento\View\Action\LayoutServiceInterface
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
     * @return \Magento\App\ActionInterface
     */
    public function addActionLayoutHandles();
}