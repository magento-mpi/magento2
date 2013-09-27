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
     * Return search client
     *
     * @param array $options
     * @return SolrClient
     */
    public function createClient(array $options = array())
    {
        return $this->_objectManager->create('SolrClient', array('options' => $options));
    }

    /**
     * Return search adapter
     *
     * @return \Magento\Search\Model\Adapter\PhpExtension
     */
    public function createAdapter()
    {
        return $this->_objectManager->create('Magento\Search\Model\Adapter\PhpExtension');
    }
}
