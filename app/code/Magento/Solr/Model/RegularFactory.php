<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model;

class RegularFactory implements \Magento\Solr\Model\FactoryInterface
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createClient(array $options = array())
    {
        return $this->_objectManager->create('Magento\Solr\Model\Client\Solr', array('options' => $options));
    }

    /**
     * {@inheritdoc}
     */
    public function createAdapter()
    {
        return $this->_objectManager->create('Magento\Solr\Model\Adapter\HttpStream');
    }
}
