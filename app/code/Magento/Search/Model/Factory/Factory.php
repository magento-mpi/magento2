<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Solr factories maker
 */
class Magento_Search_Model_Factory_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Search_Model_Solr_State
     */
    protected $_solrState;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Search_Model_Solr_State $solrState
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Search_Model_Solr_State $solrState
    ) {
        $this->_objectManager = $objectManager;
        $this->_solrState = $solrState;
    }

    /**
     * Return Factory implementation depending on solr extension loaded
     *
     * @return Magento_Search_Model_FactoryInterface
     */
    public function getFactory()
    {
        if ($this->_solrState->isActive()) {
            return $this->_objectManager->create('Magento_Search_Model_SolrFactory');
        } else {
            return $this->_objectManager->create('Magento_Search_Model_RegularFactory');
        }
    }
}
