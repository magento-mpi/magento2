<?php
/**
 * Queue client config interface
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_JobQueue_Client_ConfigInterface
{
    /**
     * Retrieve comma spearated list of queue servers
     *
     * @return string
     */
    public function getServers();
}
