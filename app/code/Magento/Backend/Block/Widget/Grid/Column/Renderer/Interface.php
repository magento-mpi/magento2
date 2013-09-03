<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend grid item renderer interface
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

interface Magento_Backend_Block_Widget_Grid_Column_Renderer_Interface
{
    /**
     * Set column for renderer
     *
     * @abstract
     * @param $column
     * @return void
     */
    public function setColumn($column);

    /**
     * Returns row associated with the renderer
     *
     * @abstract
     * @return void
     */
    public function getColumn();

    /**
     * Renders grid column
     *
     * @param \Magento\Object $row
     */
    public function render(\Magento\Object $row);
}
