<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance history grid
 */
namespace Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance\Balance\History;

class Grid
    extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * @var \Magento\CustomerBalance\Model\Resource\Balance\Collection
     */
    protected $_collection;

    /**
     * @var \Magento\CustomerBalance\Model\Balance\History
     */
    protected $_history;

    /**
     * @var \Magento\CustomerBalance\Model\Balance\HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @var \Magento\Core\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Core\Model\System\Store $systemStore
     * @param \Magento\CustomerBalance\Model\Balance\History $history
     * @param \Magento\CustomerBalance\Model\Balance\HistoryFactory $historyFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\System\Store $systemStore,
        \Magento\CustomerBalance\Model\Balance\History $history,
        \Magento\CustomerBalance\Model\Balance\HistoryFactory $historyFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->_historyFactory = $historyFactory;
        $this->_history = $history;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Initialize some params
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('historyGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('updated_at');
    }

    /**
     * Prepare grid collection
     *
     * @return \Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance\Balance_History_Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_historyFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance\Balance_History_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('updated_at', array(
            'header'    => __('Date'),
            'index'     => 'updated_at',
            'type'      => 'datetime',
            'filter'    => false,
            'width'     => 200,
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => __('Website'),
                'index'     => 'website_id',
                'type'      => 'options',
                'options'   => $this->_systemStore->getWebsiteOptionHash(),
                'sortable'  => false,
                'width'     => 200,
            ));
        }

        $this->addColumn('balance_action', array(
            'header'    => __('Action'),
            'width'     => 70,
            'index'     => 'action',
            'sortable'  => false,
            'type'      => 'options',
            'options'   => $this->_history->getActionNamesArray()
        ));

        $this->addColumn('balance_delta', array(
            'header'    => __('Balance Change'),
            'width'     => 50,
            'index'     => 'balance_delta',
            'type'      => 'price',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'Magento\CustomerBalance\Block\Adminhtml\Widget\Grid\Column\Renderer\Currency',
        ));

        $this->addColumn('balance_amount', array(
            'header'    => __('Balance'),
            'width'     => 50,
            'index'     => 'balance_amount',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'Magento\CustomerBalance\Block\Adminhtml\Widget\Grid\Column\Renderer\Currency',
        ));

        $this->addColumn('is_customer_notified', array(
            'header'    => __('Customer notified'),
            'index'     => 'is_customer_notified',
            'type'      => 'options',
            'options'   => array(
                '1' => __('Notified'),
                '0' => __('No'),
            ),
            'sortable'  => false,
            'filter'    => false,
            'width'     => 75,
        ));

        $this->addColumn('additional_info', array(
            'header'    => __('More information'),
            'index'     => 'additional_info',
            'sortable'  => false,
        ));

        return parent::_prepareColumns();
    }

    /**
     * Row click callback
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/gridHistory', array('_current'=> true));
    }
}
