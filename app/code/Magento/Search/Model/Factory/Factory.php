<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Factory;

/**
 * Solr factories maker
 */
class Factory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Solr state
     *
     * @var \Magento\Search\Model\Solr\State
     */
    protected $_solrState;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Search\Model\Solr\State $solrState
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
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
            return $this->_objectManager->get('Magento\Search\Model\SolrFactory');
        } else {
            return $this->_objectManager->get('Magento\Search\Model\RegularFactory');
        }
    }
}
