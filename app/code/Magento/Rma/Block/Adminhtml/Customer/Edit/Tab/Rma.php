<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order RMA Grid
 */
namespace Magento\Rma\Block\Adminhtml\Customer\Edit\Tab;

class Rma
    extends \Magento\Rma\Block\Adminhtml\Rma\Grid
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Rma\Model\RmaFactory $rmaFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory,
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $collectionFactory, $rmaFactory, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('customer_edit_tab_rma');
        $this->setUseAjax(true);
    }

    /**
     * Prepare massaction
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Configuring and setting collection
     *
     * @return \Magento\Rma\Block\Adminhtml\Customer\Edit\Tab\Rma
     */
    protected function _beforePrepareCollection()
    {
        $customerId = null;
        $customer = $this->_coreRegistry->registry('current_customer');
        if ($customer && $customer->getId()) {
            $customerId = $customer->getId();
        } elseif ($this->getCustomerId()) {
            $customerId = $this->getCustomerId();
        }
        if ($customerId) {
            /** @var $collection \Magento\Rma\Model\Resource\Rma\Grid\Collection */
            $collection = $this->_collectionFactory->create();
            $collection->addFieldToFilter('customer_id', $customerId);

            $this->setCollection($collection);
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
    }

    /**
     * Get Url to action
     *
     * @param  string $action action Url part
     * @return string
     */
    protected function _getControllerUrl($action = '')
    {
        return '*/rma/' . $action;
    }

    /**
     * Get Url to action to reload grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/rma/rmaCustomer', array('_current' => true));
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * ######################## TAB settings #################################
     */
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Returns');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Returns');
    }

    /**
     * Check if can show tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        return (bool)$customer->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
