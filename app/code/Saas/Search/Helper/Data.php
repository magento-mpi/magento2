<?php
/**
 * Saas Search Helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Search_Helper_Data extends Enterprise_Search_Helper_Data
{
    /**
     * Retrieve Solr servers config
     *
     * @return array
     */
    public function getSolrServers()
    {
        $path = $this->getSolrConfigData('server_path');
        $servers = Mage::getConfig()->getNode('global/search/solr/servers')->asArray();
        $serversArray = array();
        foreach ($servers as $type => $group) {
            foreach ($group as $server) {
                $serversArray[$type][] = array(
                    "host"    => $server['host'],
                    "port"    => $server['port'],
                    "timeout" => $server['timeout'],
                    "path"    => $path,
                    "login"   => false,
                    "password"=> false,
                );
            }
        }
        return $serversArray;
    }
}
