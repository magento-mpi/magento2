<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward points balance grid
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
    extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * Flag to store if customer has orphan points
     *
     * @var boolean
     */
    protected $_customerHasOrphanPoints = false;

    /**
     * Reward data
     *
     * @var Magento_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Reward_Model_Resource_Reward_CollectionFactory
     */
    protected $_rewardsFactory;

    /**
     * @var Magento_Reward_Model_Source_WebsiteFactory
     */
    protected $_websitesFactory;

    /**
     * @param Magento_Reward_Helper_Data $rewardData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Reward_Model_Resource_Reward_CollectionFactory $rewardsFactory
     * @param Magento_Reward_Model_Source_WebsiteFactory $websitesFactory
     * @param array $data
     */
    public function __construct(
        Magento_Reward_Helper_Data $rewardData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Reward_Model_Resource_Reward_CollectionFactory $rewardsFactory,
        Magento_Reward_Model_Source_WebsiteFactory $websitesFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_rewardData = $rewardData;
        $this->_rewardsFactory = $rewardsFactory;
        $this->_websitesFactory = $websitesFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rewardPointsBalanceGrid');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    /**
     * Getter
     *
     * @return Magento_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->_coreRegistry->registry('current_customer');
    }

    /**
     * Prepare grid collection
     *
     * @return Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_rewardsFactory->addFieldToFilter('customer_id', $this->getCustomer()->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * After load collection processing
     *
     * @return Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
     */
    protected function _afterLoadCollection()
    {
        parent::_afterLoadCollection();
        /* @var $item Magento_Reward_Model_Reward */
        foreach ($this->getCollection() as $item) {
            $website = $item->getData('website_id');
            if ($website !== null) {
                $minBalance = $this->_rewardData->getGeneralConfig(
                    'min_points_balance',
                    (int)$website
                );
                $maxBalance = $this->_rewardData->getGeneralConfig(
                    'max_points_balance',
                    (int)$website
                );
                $item->addData(array(
                    'min_points_balance' => (int)$minBalance,
                    'max_points_balance' => (!((int)$maxBalance)?__('Unlimited'):$maxBalance)
                ));
            } else {
                $this->_customerHasOrphanPoints = true;
                $item->addData(array(
                    'min_points_balance' => __('No Data'),
                    'max_points_balance' => __('No Data')
                ));
            }
            $item->setCustomer($this->getCustomer());
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Magento_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid
     */
    protected function _prepareColumns()
    {
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'   => __('Website'),
                'index'    => 'website_id',
                'sortable' => false,
                'type'     => 'options',
                'options'  => $this->_websitesFactory->create()->toOptionArray(false)
            ));
        }

        $this->addColumn('points_balance', array(
            'header'   => __('Balance'),
            'index'    => 'points_balance',
            'sortable' => false,
            'align'    => 'center'
        ));

        $this->addColumn('currency_amount', array(
            'header'   => __('Currency Amount'),
            'getter'   => 'getFormatedCurrencyAmount',
            'align'    => 'right',
            'sortable' => false
        ));

        $this->addColumn('min_balance', array(
            'header'   => __('Reward Points Threshold'),
            'index'    => 'min_points_balance',
            'sortable' => false,
            'align'    => 'center'
        ));

        $this->addColumn('max_balance', array(
            'header'   => __('Reward Points Cap'),
            'index'    => 'max_points_balance',
            'sortable' => false,
            'align'    => 'center'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return url to delete orphan points
     *
     * @return string
     */
    public function getDeleteOrphanPointsUrl()
    {
        return $this->getUrl('*/customer_reward/deleteOrphanPoints', array('_current' => true));
    }

    /**
     * Processing block html after rendering.
     * Add button to delete orphan points if customer has such points
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        if ($this->_customerHasOrphanPoints) {
            $deleteOrhanPointsButton = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => __('Delete Orphan Points'),
                    'onclick'   => 'setLocation(\'' . $this->getDeleteOrphanPointsUrl() .'\')',
                    'class'     => 'scalable delete',
                ));
            $html .= $deleteOrhanPointsButton->toHtml();
        }
        return $html;
    }

    /**
     * Return grid row url
     *
     * @param Magento_Reward_Model_Reward $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
