<?php
/**
 * Mail Sender Resolver interface
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Mail\Template;

interface SenderResolverInterface
{
    /**
     * Resolve sender data
     * @throws \Magento\Mail\Exception
     * @param string|array $sender
     * @param int|null $scopeId
     * @return array
     */
    public function resolve($sender, $scopeId = null);
}
