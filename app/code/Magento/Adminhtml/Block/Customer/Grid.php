<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * @var \Magento\Core\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Customer\Model\Resource\Customer\CollectionFactory
     */
    protected $_customersFactory;

    /**
     * @var \Magento\Customer\Model\Resource\Group\CollectionFactory
     */
    protected $_groupsFactory;

    /**
     * @param \Magento\Core\Model\System\Store $systemStore
     * @param \Magento\Customer\Model\Resource\Customer\CollectionFactory $customersFactory
     * @param \Magento\Customer\Model\Resource\Group\CollectionFactory $groupsFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\System\Store $systemStore,
        \Magento\Customer\Model\Resource\Customer\CollectionFactory $customersFactory,
        \Magento\Customer\Model\Resource\Group\CollectionFactory $groupsFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->_customersFactory = $customersFactory;
        $this->_groupsFactory = $groupsFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_customersFactory->create()
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => __('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
        /*$this->addColumn('firstname', array(
            'header'    => __('First Name'),
            'index'     => 'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    => __('Last Name'),
            'index'     => 'lastname'
        ));*/
        $this->addColumn('name', array(
            'header'    => __('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => __('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));

        $groups = $this->_groupsFactory->create()
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  __('Group'),
            'width'     =>  '100',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));

        $this->addColumn('Telephone', array(
            'header'    => __('Phone'),
            'width'     => '100',
            'index'     => 'billing_telephone'
        ));

        $this->addColumn('billing_postcode', array(
            'header'    => __('ZIP'),
            'width'     => '90',
            'index'     => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header'    => __('Country'),
            'width'     => '100',
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header'    => __('State/Province'),
            'width'     => '100',
            'index'     => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header'    => __('Customer Since'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => __('Web Site'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => $this->_systemStore->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }

        $this->addColumn('action',
            array(
                'header'    =>  __('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => __('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportXml', __('Excel XML'));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customer');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => __('Delete'),
             'url'      => $this->getUrl('adminhtml/*/massDelete'),
             'confirm'  => __('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('newsletter_subscribe', array(
             'label'    => __('Subscribe to Newsletter'),
             'url'      => $this->getUrl('adminhtml/*/massSubscribe')
        ));

        $this->getMassactionBlock()->addItem('newsletter_unsubscribe', array(
             'label'    => __('Unsubscribe from Newsletter'),
             'url'      => $this->getUrl('adminhtml/*/massUnsubscribe')
        ));

        $groups = $this->helper('Magento\Customer\Helper\Data')->getGroups()->toOptionArray();

        array_unshift($groups, array('label'=> '', 'value'=> ''));
        $this->getMassactionBlock()->addItem('assign_group', array(
             'label'        => __('Assign a Customer Group'),
             'url'          => $this->getUrl('adminhtml/*/massAssignGroup'),
             'additional'   => array(
                'visibility'    => array(
                     'name'     => 'group',
                     'type'     => 'select',
                     'class'    => 'required-entry',
                     'label'    => __('Group'),
                     'values'   => $groups
                 )
            )
        ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/*/grid', array('_current'=> true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/*/edit', array('id'=>$row->getId()));
    }
}
