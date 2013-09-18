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
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
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
