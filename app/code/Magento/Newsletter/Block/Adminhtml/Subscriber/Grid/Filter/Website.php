<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter subscribers grid website filter
 */
namespace Magento\Newsletter\Block\Adminhtml\Subscriber\Grid\Filter;

use Magento\Store\Model\Resource\Website\Collection;

class Website extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * Website collection
     *
     * @var Collection
     */
    protected $_websiteCollection = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Model\Resource\Website\CollectionFactory
     */
    protected $_websitesFactory;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Magento\Store\Model\Resource\Website\CollectionFactory $websitesFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Store\Model\Resource\Website\CollectionFactory $websitesFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_websitesFactory = $websitesFactory;
        parent::__construct($context, $resourceHelper, $data);
    }

    /**
     * Get options for grid filter
     *
     * @return array
     */
    protected function _getOptions()
    {
        $result = $this->getCollection()->toOptionArray();
        array_unshift($result, array('label' => null, 'value' => null));
        return $result;
    }

    /**
     * @return Collection|null
     */
    public function getCollection()
    {
        if (is_null($this->_websiteCollection)) {
            $this->_websiteCollection = $this->_websitesFactory->create()->load();
        }

        $this->_coreRegistry->register('website_collection', $this->_websiteCollection);

        return $this->_websiteCollection;
    }

    /**
     * Get options for grid filter
     *
     * @return null|array
     */
    public function getCondition()
    {
        $id = $this->getValue();
        if (!$id) {
            return null;
        }

        $website = $this->_storeManager->getWebsite($id);
        return array('in' => $website->getStoresIds(true));
    }
}
