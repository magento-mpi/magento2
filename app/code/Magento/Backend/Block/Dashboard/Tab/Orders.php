<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard orders diagram
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Dashboard\Tab;

class Orders extends \Magento\Backend\Block\Dashboard\Graph
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Reports\Model\Resource\Order\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Helper\Dashboard\Data $dashboardData
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param \Magento\Backend\Helper\Dashboard\Order $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Reports\Model\Resource\Order\CollectionFactory $collectionFactory,
        \Magento\Backend\Helper\Dashboard\Data $dashboardData,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Backend\Helper\Dashboard\Order $dataHelper,
        array $data = array()
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $collectionFactory, $dashboardData, $localeLists, $data);
    }

    /**
     * Initialize object
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setHtmlId('orders');
        parent::_construct();
    }

    /**
     * Prepare chart data
     *
     * @return void
     */
    protected function _prepareData()
    {
        $this->getDataHelper()->setParam('store', $this->getRequest()->getParam('store'));
        $this->getDataHelper()->setParam('website', $this->getRequest()->getParam('website'));
        $this->getDataHelper()->setParam('group', $this->getRequest()->getParam('group'));

        $this->setDataRows('quantity');
        $this->_axisMaps = array('x' => 'range', 'y' => 'quantity');

        parent::_prepareData();
    }
}
