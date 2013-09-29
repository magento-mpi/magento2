<?php
/**
 * FPC container interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model;

interface ContainerInterface
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
     * @return \Magento\FullPageCache\Model\ContainerInterface
     */
    public function saveCache($blockContent);

    /**
     * Set processor for container needs
     *
     * @param \Magento\FullPageCache\Model\Processor $processor
     * @return \Magento\FullPageCache\Model\ContainerInterface
     */
    public function setProcessor(\Magento\FullPageCache\Model\Processor $processor);
}
