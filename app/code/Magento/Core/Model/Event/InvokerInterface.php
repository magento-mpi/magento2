<?php
/**
 * Event invoker interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Event;

interface InvokerInterface
{
    /**
     * Dispatch event
     *
     * @param array $configuration
     * @param \Magento\Event\Observer $observer
     */
    public function dispatch(array $configuration, \Magento\Event\Observer $observer);
}
