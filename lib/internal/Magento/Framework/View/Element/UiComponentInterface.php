<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Element;

/**
 * Class UiComponentInterface
 */
interface UiComponentInterface extends BlockInterface
{
    /**
     * Update component data
     *
     * @param array $arguments
     * @return string
     */
    public function update(array $arguments = []);

    /**
     * Prepare component data
     *
     * @return void
     */
    public function prepare();

    /**
     * Render component
     *
     * @return string
     */
    public function render();

    /**
     * Render label
     *
     * @return mixed|string
     */
    public function renderLabel();

    /**
     * Getting template for rendering content
     *
     * @return string|false
     */
    public function getContentTemplate();

    /**
     * Getting template for rendering label
     *
     * @return string|false
     */
    public function getLabelTemplate();

    /**
     * Getting instance name
     *
     * @return string
     */
    public function getName();

    /**
     * Getting parent name component instance
     *
     * @return string
     */
    public function getParentName();

    /**
     * Get render context
     *
     * @return Context
     */
    public function getRenderContext();

    /**
     * Get elements
     *
     * @return UiComponentInterface[]
     */
    public function getElements();

    /**
     * Set elements
     *
     * @param array $elements
     */
    public function setElements(array $elements);
}
