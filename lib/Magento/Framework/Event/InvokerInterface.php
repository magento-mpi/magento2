<?php
/**
 * Event invoker interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Event;

interface InvokerInterface
{
    /**
     * Dispatch event
     *
     * @param array $configuration
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function dispatch(array $configuration, \Magento\Framework\Event\Observer $observer);
}
