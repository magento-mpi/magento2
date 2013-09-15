<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer recurring profiles tab
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Customer\Edit\Tab\Recurring;

class Profile
    extends \Magento\Sales\Block\Adminhtml\Recurring\Profile\Grid
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Helper_Data $coreData,
        Magento_Payment_Helper_Data $paymentData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $coreData, $paymentData, $context, $storeManager, $urlModel, $data
        );
    }

    /**
     * Disable filters and paging
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_edit_tab_recurring_profile');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Recurring Billing Profiles (beta)');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Recurring Billing Profiles (beta)');
    }

    /**
     * Can show tab in tabs
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

    /**
     * Prepare collection for grid
     *
     * @return \Magento\Sales\Block\Adminhtml\Customer\Edit\Tab\Recurring\Profile
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getResourceModel('Magento\Sales\Model\Resource\Recurring\Profile\Collection')
            ->addFieldToFilter('customer_id', $this->_coreRegistry->registry('current_customer')->getId());
        if (!$this->getParam($this->getVarNameSort())) {
            $collection->setOrder('profile_id', 'desc');
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'orders';
    }

    /**
     * Return grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/sales_recurring_profile/customerGrid', array('_current' => true));
    }
}
