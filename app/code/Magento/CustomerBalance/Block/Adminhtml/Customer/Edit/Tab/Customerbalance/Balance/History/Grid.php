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
class Magento_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
    extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magento_CustomerBalance_Model_Resource_Balance_Collection
     */
    protected $_collection;

    /**
     * @var Magento_CustomerBalance_Model_Balance_History
     */
    protected $_history;

    /**
     * @var Magento_CustomerBalance_Model_Balance_HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @param Magento_Core_Model_System_Store $systemStore
     * @param Magento_CustomerBalance_Model_Balance_History $history
     * @param Magento_CustomerBalance_Model_Balance_HistoryFactory $historyFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_System_Store $systemStore,
        Magento_CustomerBalance_Model_Balance_History $history,
        Magento_CustomerBalance_Model_Balance_HistoryFactory $historyFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
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
     * @return Magento_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
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
     * @return Magento_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
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
            'renderer'  => 'Magento_CustomerBalance_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency',
        ));

        $this->addColumn('balance_amount', array(
            'header'    => __('Balance'),
            'width'     => 50,
            'index'     => 'balance_amount',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'Magento_CustomerBalance_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency',
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
