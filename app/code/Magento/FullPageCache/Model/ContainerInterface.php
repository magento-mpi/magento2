<?php
/**
 * FPC container interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_FullPageCache_Model_ContainerInterface
{
    /**
     * Generate placeholder content before application was initialized and apply to page content if possible
     *
     * @param string $content
     * @return bool
     */
    public function applyWithoutApp(&$content);

    /**
     * Generate and apply container content in controller after application is initialized
     *
     * @param string $content
     * @return bool
     */
    public function applyInApp(&$content);

    /**
     * Save rendered block content to cache storage
     *
     * @param string $blockContent
     * @return Magento_FullPageCache_Model_ContainerInterface
     */
    public function saveCache($blockContent);

    /**
     * Set processor for container needs
     *
     * @param Magento_FullPageCache_Model_Processor $processor
     * @return Magento_FullPageCache_Model_ContainerInterface
     */
    public function setProcessor(Magento_FullPageCache_Model_Processor $processor);
}
