<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

use Magento\Ui\ContentType\Builders\ConfigBuilderInterface;

/**
 * Class ViewInterface
 */
interface ViewInterface
{
    /**
     * prepare component data
     *
     * @param array $arguments
     * @return string
     */
    public function prepare(array $arguments = []);

    /**
     * Render component
     *
     * @return string
     */
    public function render();

    /**
     * @param array $arguments
     * @return string
     */
    public function renderLabel(array $arguments = []);

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
     * Get component configuration
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration();

    /**
     * Get render context
     *
     * @return Context
     */
    public function getRenderContext();

    /**
     * Get configuration builder
     *
     * @return ConfigBuilderInterface
     */
    public function getConfigurationBuilder();
}
