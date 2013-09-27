<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter subscribers grid website filter
 */
class Magento_Adminhtml_Block_Newsletter_Subscriber_Grid_Filter_Website
    extends Magento_Backend_Block_Widget_Grid_Column_Filter_Select
{
    protected $_websiteCollection = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_Resource_Website_CollectionFactory
     */
    protected $_websitesFactory;

    /**
     * @param Magento_Core_Model_Resource_Website_CollectionFactory $websitesFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Core_Model_Resource_Helper $resourceHelper
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Resource_Website_CollectionFactory $websitesFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Backend_Block_Context $context,
        Magento_Core_Model_Resource_Helper $resourceHelper,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_websitesFactory = $websitesFactory;
        parent::__construct($context, $resourceHelper, $data);
    }

    protected function _getOptions()
    {
        $result = $this->getCollection()->toOptionArray();
        array_unshift($result, array('label'=>null, 'value'=>null));
        return $result;
    }

    /**
     * @return Magento_Core_Model_Resource_Website_Collection|null
     */
    public function getCollection()
    {
        if (is_null($this->_websiteCollection)) {
            $this->_websiteCollection = $this->_websitesFactory->create()->load();
        }

        $this->_coreRegistry->register('website_collection', $this->_websiteCollection);

        return $this->_websiteCollection;
    }

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
