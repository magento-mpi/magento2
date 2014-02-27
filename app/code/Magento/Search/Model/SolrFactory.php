<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

class SolrFactory implements \Magento\Search\Model\FactoryInterface
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
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
        return $this->_objectManager->create('Magento\Search\Model\Adapter\PhpExtension');
    }
}
