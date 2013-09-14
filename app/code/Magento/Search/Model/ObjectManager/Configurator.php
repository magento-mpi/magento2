<?php
/**
 * Object manager configurator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\ObjectManager;

class Configurator implements \Magento\Core\Model\ObjectManager\DynamicConfigInterface
{
    /**
     * Retrieve runtime environment specific di configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        if (extension_loaded('solr')) {
            $adapter = 'Magento\Search\Model\Adapter\PhpExtension';
            $clientFactory = 'Magento\Search\Model\Client\SolrClient\Factory';
        } else {
            $adapter = 'Magento\Search\Model\Adapter\HttpStream';
            $clientFactory = 'Magento\Search\Model\Client\Solr\Factory';
        }
        return array(
            'preferences' => array(
                'Magento\Search\Model\AdapterInterface' => $adapter,
                'Magento\Search\Model\Client\FactoryInterface' => $clientFactory
            )
        );
    }
}
