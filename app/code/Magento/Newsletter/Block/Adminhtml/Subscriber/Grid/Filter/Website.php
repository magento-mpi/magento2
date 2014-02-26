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

use Magento\Core\Model\Resource\Website\Collection;

class Website
    extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
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
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Core\Model\Resource\Website\CollectionFactory
     */
    protected $_websitesFactory;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Model\Resource\Helper $resourceHelper
     * @param \Magento\Core\Model\Resource\Website\CollectionFactory $websitesFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\Resource\Helper $resourceHelper,
        \Magento\Core\Model\Resource\Website\CollectionFactory $websitesFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Registry $registry,
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
        array_unshift($result, array('label'=>null, 'value'=>null));
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
