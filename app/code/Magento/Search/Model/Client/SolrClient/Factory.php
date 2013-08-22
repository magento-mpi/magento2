<?php
/**
 * Search client Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Search_Model_Client_SolrClient_Factory implements Magento_Search_Model_Client_FactoryInterface
{
    /**
     * Constructor
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
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
