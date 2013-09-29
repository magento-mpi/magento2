<?php
/**
 * Queue client config interface
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\JobQueue\Client;

interface ConfigInterface
{
    /**
     * Retrieve comma spearated list of queue servers
     *
     * @return string
     */
    public function getServers();
}
