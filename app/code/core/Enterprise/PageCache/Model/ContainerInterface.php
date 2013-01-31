<?php
/**
 * FPC container interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Enterprise_PageCache_Model_ContainerInterface
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
     * @return Enterprise_PageCache_Model_ContainerInterface
     */
    public function saveCache($blockContent);

    /**
     * Set processor for container needs
     *
     * @param Mage_Core_Model_Cache_ProcessorInterface $processor
     * @return Enterprise_PageCache_Model_ContainerInterface
     */
    public function setProcessor(Mage_Core_Model_Cache_ProcessorInterface $processor);
}
