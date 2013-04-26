<?php
/**
 * Queue client config interface
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Queue_Client_ConfigInterface
{
    /**
     * Retrieve comma spearated list of queue servers
     *
     * @return string
     */
    public function getServers();

    /**
     * Retrieve additional params for every task
     *
     * @return array
     */
    public function getTaskParams();
}
