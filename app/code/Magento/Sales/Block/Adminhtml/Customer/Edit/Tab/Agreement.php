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
 * Adminhtml customer billing agreement tab
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Customer\Edit\Tab;

class Agreement
    extends \Magento\Sales\Block\Adminhtml\Billing\Agreement\Grid
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Columns, that should be removed from grid
     *
     * @var array
     */
    protected $_columnsToRemove = array('customer_email', 'customer_firstname', 'customer_lastname');

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
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
        parent::__construct($coreData, $paymentData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Disable filters and paging
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_edit_tab_agreements');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Billing Agreements');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Billing Agreements');
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

    public function getGridUrl()
    {
        return $this->getUrl('*/sales_billing_agreement/customerGrid', array('_current'=>true));
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
     * Prepare collection for grid
     *
     * @return \Magento\Sales\Block\Adminhtml\Customer\Edit\Tab\Agreement
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getResourceModel('Magento\Sales\Model\Resource\Billing\Agreement\Collection')
            ->addFieldToFilter('customer_id', $this->_coreRegistry->registry('current_customer')->getId())
            ->setOrder('created_at');
        $this->setCollection($collection);
        return \Magento\Adminhtml\Block\Widget\Grid::_prepareCollection();
    }

    /**
     * Remove some columns and make other not sortable
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareColumns()
    {
        $result = parent::_prepareColumns();

        foreach ($this->getColumns() as $key => $value) {
            if (in_array($key, $this->_columnsToRemove)) {
                $this->removeColumn($key);
            }
        }
        return $result;
    }
}
