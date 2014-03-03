<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column;

/**
 * Backend grid item renderer interface
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface RendererInterface
{
    /**
     * Set column for renderer
     *
     * @param Column $column
     * @return void
     * @abstract
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
     * @return string
     */
    public function render(\Magento\Object $row);
}
