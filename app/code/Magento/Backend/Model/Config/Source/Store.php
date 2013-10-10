<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Source;

class Store implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var \Magento\Core\Model\Resource\Store\CollectionFactory
     */
    protected $_storesFactory;

    /**
     * @param \Magento\Core\Model\Resource\Store\CollectionFactory $storesFactory
     */
    public function __construct(\Magento\Core\Model\Resource\Store\CollectionFactory $storesFactory)
    {
        $this->_storesFactory = $storesFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            /** @var $stores \Magento\Core\Model\Resource\Store\Collection */
            $stores = $this->_storesFactory->create();
            $this->_options = $stores->load()->toOptionArray();
        }
        return $this->_options;
    }
}
