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
namespace Magento\Search\Model\Factory;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Search\Model\Solr\State
     */
    protected $_solrState;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Search\Model\Solr\State $solrState
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Search\Model\Solr\State $solrState
    ) {
        $this->_objectManager = $objectManager;
        $this->_solrState = $solrState;
    }

    /**
     * Return Factory implementation depending on solr extension loaded
     *
     * @return \Magento\Search\Model\FactoryInterface
     */
    public function getFactory()
    {
        if ($this->_solrState->isActive()) {
            return $this->_objectManager->create('Magento\Search\Model\SolrFactory');
        } else {
            return $this->_objectManager->create('Magento\Search\Model\RegularFactory');
        }
    }
}
