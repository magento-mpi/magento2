<?php
/**
 * Search client Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Client\SolrClient;

class Factory implements \Magento\Search\Model\Client\FactoryInterface
{
    /**
     * Constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return search client interface
     *
     * @param $options
     * @return mixed
     */
    public function createClient($options)
    {
        return $this->_objectManager->create('SolrClient', array('clientOptions' => $options));
    }
}
