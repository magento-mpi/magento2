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
 * Recurring profiles grid
 */
namespace Magento\Sales\Block\Adminhtml\Recurring\Profile;

class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Set ajax/session parameters
     */
    /**
     * Payment data
     *
     * @var Magento_Payment_Helper_Data
     */
    protected $_paymentData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Payment_Helper_Data $paymentData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_paymentData = $paymentData;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_recurring_profile_grid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return \Magento\Sales\Block\Adminhtml\Recurring\Profile\Grid
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getResourceModel('Magento\Sales\Model\Resource\Recurring\Profile\Collection');
        $this->setCollection($collection);
        if (!$this->getParam($this->getVarNameSort())) {
            $collection->setOrder('profile_id', 'desc');
        }
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\Sales\Block\Adminhtml\Recurring\Profile\Grid
     */
    protected function _prepareColumns()
    {
        $profile = \Mage::getModel('Magento\Sales\Model\Recurring\Profile');

        $this->addColumn('reference_id', array(
            'header' => $profile->getFieldLabel('reference_id'),
            'index' => 'reference_id',
            'html_decorators' => array('nobr'),
            'width' => 1,
        ));

        if (!\Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'     => __('Store'),
                'index'      => 'store_id',
                'type'       => 'store',
                'store_view' => true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('state', array(
            'header' => $profile->getFieldLabel('state'),
            'index' => 'state',
            'type'  => 'options',
            'options' => $profile->getAllStates(),
            'html_decorators' => array('nobr'),
            'width' => 1,
        ));

        $this->addColumn('created_at', array(
            'header' => $profile->getFieldLabel('created_at'),
            'index' => 'created_at',
            'type' => 'datetime',
            'html_decorators' => array('nobr'),
            'width' => 1,
        ));

        $this->addColumn('updated_at', array(
            'header' => $profile->getFieldLabel('updated_at'),
            'index' => 'updated_at',
            'type' => 'datetime',
            'html_decorators' => array('nobr'),
            'width' => 1,
        ));

        $methods = array();
        foreach ($this->_paymentData->getRecurringProfileMethods() as $method) {
            $methods[$method->getCode()] = $method->getTitle();
        }
        $this->addColumn('method_code', array(
            'header'  => $profile->getFieldLabel('method_code'),
            'index'   => 'method_code',
            'type'    => 'options',
            'options' => $methods,
        ));

        $this->addColumn('schedule_description', array(
            'header' => $profile->getFieldLabel('schedule_description'),
            'index' => 'schedule_description',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return row url for js event handlers
     *
     * @param \Magento\Object
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_recurring_profile/view', array('profile' => $row->getId()));
    }

    /**
     * Return grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
