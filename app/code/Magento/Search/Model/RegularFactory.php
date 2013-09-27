<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Search_Model_RegularFactory implements Magento_Search_Model_FactoryInterface
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return search client
     *
     * @param array $options
     * @return Magento_Search_Model_Client_Solr
     */
    public function createClient(array $options = array())
    {
        return $this->_objectManager->create('Magento_Search_Model_Client_Solr', array('clientOptions' => $options));
    }

    /**
     * Return search adapter
     *
     * @return Magento_Search_Model_Adapter_HttpStream
     */
    public function createAdapter()
    {
        return $this->_objectManager->create('Magento_Search_Model_Adapter_HttpStream');
    }
}
