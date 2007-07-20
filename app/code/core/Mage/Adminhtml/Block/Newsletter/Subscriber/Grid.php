<?php
/**
 * Adminhtml newsletter subscribers grid block
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
	/**
	 * Constructor
	 *
	 * Set main configuration of grid
	 */
	public function __construct()
    {
        parent::__construct();
        
        $this->setId('subscriberGrid');
        $this->setDefaultSort('id', 'desc');
        $this->setSaveParametersInSession(true);
        $this->setMessageBlockVisibility(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid 
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('newsletter/subscriber_collection')
			->showCustomerInfo(true)
			->showStoreInfo();
			
		if($this->getRequest()->getParam('queue', false)) {
			$collection->useQueue(Mage::getModel('newsletter/queue')
				->load($this->getRequest()->getParam('queue')));
		}
		
        $this->setCollection($collection);
		
        return parent::_prepareCollection();
    }
    
    public function getShowQueueAdd() 
    {
    	return $this->getCollection()->getSize() > 0;
    }
    
    
    
    protected function _prepareColumns()
    {
    	/*if($this->getShowQueueAdd()) {*/
    	$this->addColumn('checkbox', array(
    		'align'		=> 'center',
    		'sortable' 	=> false,
    		'filter'	=> 'adminhtml/newsletter_subscriber_grid_filter_checkbox',
    		'renderer'	=> 'adminhtml/newsletter_subscriber_grid_renderer_checkbox',
    		'width'		=> '20px'
    	));
    	/*}*/
    	
    	$this->addColumn('id', array(
    		'header'	=> __('ID'),
    		'align'		=> 'center',
    		'index'		=> 'subscriber_id'
    	));
    	
    	$this->addColumn('email', array(
    		'header'	=> __('Email'),
    		'align'		=> 'center',
    		'index'		=> 'subscriber_email'
    	));
    	
    	$this->addColumn('name', array(
    		'header'	=> __('Name'),
    		'align'		=> 'center',
    		'index'		=> 'customer_name',
    		'sortable' 	=> false,
    		'filter'	=> false,
    		'default'	=>	'----'
    	));
    	
    	$this->addColumn('status', array(
    		'align'		=> 'center',
    		'header'	=> __('Status'),
    		'filter'	=> 'adminhtml/newsletter_subscriber_grid_filter_status',
    		'renderer'	=> 'adminhtml/newsletter_subscriber_grid_renderer_status',
    		'index'		=> 'subscriber_status'
    	));     	
    	
    	$this->addColumn('website', array(
    		'align'		=> 'center',
    		'sortable' 	=> false,
    		'header'	=> __('Website'),
    		'filter'	=> 'adminhtml/newsletter_subscriber_grid_filter_website',
    		'renderer'	=> 'adminhtml/newsletter_subscriber_grid_renderer_website',
    		'index'		=> 'store_id'
    	)); 
    	
    	
    	
    	return parent::_prepareColumns();
    }
	
}// Class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid END
