<?php
/**
 * Adminhtml newsletter problem grid block
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Problem_Grid extends Mage_Adminhtml_Block_Widget_Grid	
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('problemGrid');
		$this->setSaveParametersInSession(true);
        $this->setMessageBlockVisibility(true);
        $this->setUseAjax(true);
	}
	
	protected function _prepareCollection() 
	{
		$collection = Mage::getResourceSingleton('newsletter/problem_collection');
		
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	
	
	protected function _prepareColumns()
	{
		$this->addColumn('checkbox', array(
     		'sortable' 	=> false,
    		'filter'	=> 'adminhtml/newsletter_problem_grid_filter_checkbox',
    		'renderer'	=> 'adminhtml/newsletter_problem_grid_renderer_checkbox',
    		'width'		=> '20px'
    	));
		
		$this->addColumn('id', array(
			'header' => __('ID'),
			'index'  => 'problem_id',
			'width'	 => '50px'
		));
		
		$this->addColumn('subscriber', array(
			'header' => __('Subscriber'),
			'index'  => 'subscriber_id',
			'format' => '#$subscriber_id $customer_name ($subscriber_email)'
		));
		
		$this->addColumn('queue_id', array(
			'header' => __('Queue ID'),
			'index'  => 'queue_id'
		));
		
		$this->addColumn('queue', array(
			'header' => __('Queue Subject'),
			'index'  => 'template_subject'
		));
		
		$this->addColumn('problem_code', array(
			'header' => __('Error Code'),
			'index'  => 'problem_error_code'
		));
		
		$this->addColumn('problem_text', array(
			'header' => __('Error Text'),
			'index'  => 'problem_error_text'
		));
		return parent::_prepareColumns();
	}
}// Class Mage_Adminhtml_Block_Newsletter_Problem_Grid END
