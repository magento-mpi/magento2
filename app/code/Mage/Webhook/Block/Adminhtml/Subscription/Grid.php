<?php
/**
 * Subscription grid
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Subscription_Grid extends Mage_Backend_Block_Widget_Grid_Extended
{
    /** @var \Mage_Webhook_Model_Subscription_Config  */
    private $_subscriptionConfig;

    /** @var \Mage_Webhook_Model_Subscription_Factory  */
    private $_subscriptionFactory;

    /**
     * Internal constructor. Override _construct(), not __construct().
     *
     * @param Mage_Webhook_Model_Subscription_Config $subscriptionConfig
     * @param Mage_Webhook_Model_Subscription_Factory $subscriptionFactory
     * @param Mage_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Mage_Webhook_Model_Subscription_Config $subscriptionConfig,
        Mage_Webhook_Model_Subscription_Factory $subscriptionFactory,
        Mage_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_subscriptionConfig = $subscriptionConfig;
        $this->_subscriptionFactory = $subscriptionFactory;
        parent::__construct($context, $storeManager, $urlModel, $data);
    }

    /**
     * Internal constructor: override this in subclasses
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('subscriptionGrid');
        $this->setDefaultSort('subscription_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare subscription collection
     *
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $this->_subscriptionConfig->updateSubscriptionCollection();
        $collection = $this->_subscriptionFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for subscription grid
     *
     * @return Mage_Backend_Block_Widget_Grid_Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => $this->__('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'subscription_id',
        ));

        $this->addColumn('name', array(
            'header'    => $this->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));

        $this->addColumn('version', array(
            'header'    => $this->__('Version'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'version',
        ));

        $this->addColumn('endpoint_url', array(
            'header'    => $this->__('Endpoint URL'),
            'align'     => 'left',
            'index'     => 'endpoint_url',
        ));

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'align'     =>'left',
            'index'     => 'status',
            'type'      => 'options',
            'width'     => '100px',
            'options'   => $this->_getStatusOptions()
        ));

        $this->addColumn('action', array(
            'header'    =>  $this->__('Action'),
            'align'     =>  'left',
            'width'     => '80px',
            'filter'    =>  false,
            'sortable'  =>  false,
            'renderer'  =>  'Mage_Webhook_Block_Adminhtml_Subscription_Grid_Renderer_Action'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return row url for js event handlers
     *
     * @param Mage_Catalog_Model_Product|Magento_Object $row
     * @return string Row url for js event handlers
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Retrieve array of possible subscription status options
     *
     * @return array Status options for the grid
     */
    protected function _getStatusOptions()
    {
        return array(
            Mage_Webhook_Model_Subscription::STATUS_ACTIVE => $this->__('Active'),
            Mage_Webhook_Model_Subscription::STATUS_REVOKED => $this->__('Revoked'),
            Mage_Webhook_Model_Subscription::STATUS_INACTIVE => $this->__('Inactive'),
        );
    }
}
