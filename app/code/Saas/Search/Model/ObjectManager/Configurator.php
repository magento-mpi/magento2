<?php
/**
 * Object manager configurator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Search_Model_ObjectManager_Configurator implements Mage_Core_Model_ObjectManager_DynamicConfigInterface
{
    /**
     * Retrieve runtime environment specific di configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        if (extension_loaded('solr')) {
            $clientFactory = 'Saas_Search_Model_Client_Balancer_PhpExtension_Factory';
        } else {
            $clientFactory = 'Saas_Search_Model_Client_Balancer_HttpStream_Factory';
        }
        return array(
            'preferences' => array(
                'Enterprise_Search_Model_Client_FactoryInterface' => $clientFactory
            )
        );
    }
}
