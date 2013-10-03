<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model;

class RegularFactory implements \Magento\Search\Model\FactoryInterface
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
     * @return \Magento\Search\Model\Client\Solr
     */
    public function createClient(array $options = array())
    {
        return $this->_objectManager->create('Magento\Search\Model\Client\Solr', array('clientOptions' => $options));
    }

    /**
     * Return search adapter
     *
     * @return \Magento\Search\Model\Adapter\HttpStream
     */
    public function createAdapter()
    {
        return $this->_objectManager->create('Magento\Search\Model\Adapter\HttpStream');
    }
}
