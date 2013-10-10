<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for shipping table rates CSV importing
 *
 * @category   Magento
 * @package    Magento_Shipping
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Shipping\Model\Config\Backend;

class Tablerate extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Shipping\Model\Resource\Carrier\TablerateFactory
     */
    protected $_tablerateFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Shipping\Model\Resource\Carrier\TablerateFactory $tablerateFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\Shipping\Model\Resource\Carrier\TablerateFactory $tablerateFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_tablerateFactory = $tablerateFactory;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    public function _afterSave()
    {
        $this->_tablerateFactory->create()->uploadAndImport($this);
    }
}
