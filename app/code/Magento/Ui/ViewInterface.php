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
     * Render component
     *
     * @param array $arguments
     * @param string $acceptType
     * @return mixed|string
     */
    public function render(array $arguments = [], $acceptType = 'html');

    /**
     * Getting template
     *
     * @return string|false
     */
    public function getTemplate();

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
