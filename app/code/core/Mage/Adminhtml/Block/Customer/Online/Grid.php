<?php
/**
 * Adminhtml customer grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Online_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('onlineGrid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setDefaultSort('last_activity');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
    	parent::_prepareCollection();
    	foreach ($this->getCollection()->getItems() as $item) {
        	$item->addIpData($item)
                 ->addCustomerData($item)
        	     ->addQuoteData($item);
        }
        return $this;
    }

    protected function _initCollection()
    {
        $filter = $this->getRequest()->getParam('filter_value', false);

        $filterOnlineOnly = ($filter == 'filterOnline') ? false : true;
        $filterCustomersOnly = ($filter == 'filterCustomers') ? true : false;
        $filterGuestsOnly = ($filter == 'filterGuests') ? true : false;

        $collection = Mage::getResourceSingleton('log/visitor_collection');
        $collection->useOnlineFilter();
        /*
        if( $filterCustomersOnly ) {
            $collection->showCustomersOnly();
        }

        if( $filterGuestsOnly ) {
            $collection->showGuestsOnly();
        }
        */
        $this->setCollection($collection);
    }

    protected function _beforeToHtml()
    {
        $this->addColumn('id', array(
                            'header'=>__('ID'),
                            'width'=>'40px',
                            'align'=>'right',
                            'filter' => false,
                            'sortable' => false,
                            'default' => __('n/a'),
                            'index'=>'customer_id')
                        );

        $this->addColumn('firstname', array(
                            'header'=>__('First Name'),
                            'filter' => false,
                            'sortable' => false,
                            'default' => __('Guest'),
                            'index'=>'customer_firstname')
                        );

        $this->addColumn('lastname', array(
                            'header'=>__('Last Name'),
                            'filter' => false,
                            'sortable' => false,
                            'default' => __('n/a'),
                            'index'=>'customer_lastname')
                        );

        $this->addColumn('email', array(
                            'header'=>__('Email'),
                            'filter' => false,
                            'sortable' => false,
                            'default' => __('n/a'),
                            'index'=>'customer_email')
                        );

        $this->addColumn('ip_address', array(
                            'header'=>__('IP Address'),
                            'index'=>'remote_addr',
                            'default' => __('n/a'),
        	                'renderer'=>'adminhtml/customer_online_grid_renderer_ip')
        	            );

        $this->addColumn('session_start_time', array(
                            'header'=>__('Session Start Time'),
                            'align'=>'left',
                            'type' => 'datetime',
                            'default' => __('n/a'),
                            'width' => '200px',
                            'index'=>'first_visit_at')
                        );

        $this->addColumn('last_activity', array(
                            'header'=>__('Last Activity'),
                            'align'=>'left',
                            'type' => 'datetime',
                            'default' => __('n/a'),
                            'width' => '20s0px',
                            'index'=>'last_visit_at')
                        );

        $this->addColumn('last_url', array(
                            'header'=>__('Last Url'),
                            'default' => __('n/a'),
                            'index'=>'url')
                        );

        $this->_initCollection();
        return parent::_beforeToHtml();
    }

    public function getRowUrl($row)
    {
        return ( intval($row->getCustomerId()) > 0 ) ? Mage::getUrl('*/customer/edit', array('id' => $row->getCustomerId())) : '#';
    }
}
