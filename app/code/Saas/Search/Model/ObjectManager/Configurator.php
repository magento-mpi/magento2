<?php
/**
 * Object manager configurator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Search_Model_ObjectManager_Configurator extends Mage_Core_Model_ObjectManager_ConfigAbstract
{
    /**
     * Configure di instance
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function configure(Magento_ObjectManager $objectManager)
    {
        if (extension_loaded('solr')) {
            $clientFactory = 'Saas_Search_Model_Client_Balancer_PhpExtension_Factory';
        } else {
            $clientFactory = 'Saas_Search_Model_Client_Balancer_HttpStream_Factory';
        }
        $objectManager->configure(array(
            'preferences' => array(
                'Enterprise_Search_Model_Client_FactoryInterface' => $clientFactory
            )
        ));
    }
}
