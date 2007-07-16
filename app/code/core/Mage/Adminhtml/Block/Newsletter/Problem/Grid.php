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
	protected function _initCollection() 
	{
		$collection = Mage::getResourceModel('newsletter/problem_collection');
		$this->setCollection($collection);
	}
	
	protected function _initColumns()
	{
		$this->addColumn('id', array(
			'header' => __('id'),
			'index'  => 'problem_id'
		));
		
		$this->addColumn('subscriber', array(
			'header' => __('subscriber'),
			'index'  => 'subscriber_id',
		));
		
		$this->addColumn('problem_code', array(
			'header' => __('error'),
			'index'  => 'problem_error_code',
			'format' => '#$problem_error_code: $problem_error_text'
		));
	}
}// Class Mage_Adminhtml_Block_Newsletter_Problem_Grid END