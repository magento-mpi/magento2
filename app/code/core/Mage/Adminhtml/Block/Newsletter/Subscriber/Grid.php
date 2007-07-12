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
        $this->setDefaultSort('id');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid 
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('newsletter/subscriber_collection')
			->showCustomerInfo(true);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
    	$this->addColumn('checkbox', array(
    		'align'		=> 'center',
    		'sortable' 	=> false,
    		'filter'	=> 'adminhtml/newsletter_subscriber_grid_filter_checkbox',
    		'renderer'	=> 'adminhtml/newsletter_subscriber_grid_renderer_checkbox',
    		'width'		=> '20px'
    	));
    	
    	$this->addColumn('id', array(
    		'header'	=> __('id'),
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
    	
    	$this->addColumn('website', array(
    		'align'		=> 'center',
    		'sortable' 	=> false,
    		'filter'	=> 'adminhtml/newsletter_subscriber_grid_filter_website',
    	));
    	
    	return parent::_prepareColumns();
    }
	
}// Class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid END