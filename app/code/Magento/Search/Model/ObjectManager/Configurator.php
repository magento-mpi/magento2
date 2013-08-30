<?php
/**
 * Object manager configurator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Search_Model_ObjectManager_Configurator implements Magento_Core_Model_ObjectManager_DynamicConfigInterface
{
    /**
     * Retrieve runtime environment specific di configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        if (extension_loaded('solr')) {
            $adapter = 'Magento_Search_Model_Adapter_PhpExtension';
            $clientFactory = 'Magento_Search_Model_Client_SolrClient_Factory';
        } else {
            $adapter = 'Magento_Search_Model_Adapter_HttpStream';
            $clientFactory = 'Magento_Search_Model_Client_Solr_Factory';
        }
        return array(
            'preferences' => array(
                'Magento_Search_Model_AdapterInterface' => $adapter,
                'Magento_Search_Model_Client_FactoryInterface' => $clientFactory
            )
        );
    }
}
