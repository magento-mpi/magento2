<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Factory;

/**
 * Solr factories maker
 */
class Factory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Solr state
     *
     * @var \Magento\Solr\Model\Solr\State
     */
    protected $_solrState;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Solr\Model\Solr\State $solrState
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Solr\Model\Solr\State $solrState
    ) {
        $this->_objectManager = $objectManager;
        $this->_solrState = $solrState;
    }

    /**
     * Return Factory implementation depending on solr extension loaded
     *
     * @return \Magento\Solr\Model\FactoryInterface
     */
    public function getFactory()
    {
        if ($this->_solrState->isActive()) {
            return $this->_objectManager->get('Magento\Solr\Model\SolrFactory');
        } else {
            return $this->_objectManager->get('Magento\Solr\Model\RegularFactory');
        }
    }
}
