<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model;

class SolrFactory implements \Magento\Solr\Model\FactoryInterface
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createClient(array $options = array())
    {
        return $this->_objectManager->create('SolrClient', array('options' => $options));
    }

    /**
     * {@inheritdoc}
     */
    public function createAdapter()
    {
        return $this->_objectManager->create('Magento\Solr\Model\Adapter\PhpExtension');
    }
}
