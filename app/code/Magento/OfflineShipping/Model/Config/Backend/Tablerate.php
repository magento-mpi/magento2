<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\OfflineShipping\Model\Config\Backend;

use Magento\Model\AbstractModel;

/**
 * Backend model for shipping table rates CSV importing
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tablerate extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\OfflineShipping\Model\Resource\Carrier\TablerateFactory
     */
    protected $_tablerateFactory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\OfflineShipping\Model\Resource\Carrier\TablerateFactory $tablerateFactory
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\OfflineShipping\Model\Resource\Carrier\TablerateFactory $tablerateFactory,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_tablerateFactory = $tablerateFactory;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * @return AbstractModel|void
     */
    public function _afterSave()
    {
        $this->_tablerateFactory->create()->uploadAndImport($this);
    }
}
