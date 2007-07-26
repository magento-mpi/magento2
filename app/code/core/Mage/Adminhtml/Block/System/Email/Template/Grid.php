<?php
/**
 * Adminhtml  system templates grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_System_Email_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	
	protected function _construct()
	{
		$this->setEmptyText(__('No Templates Found'));
	}
	
	
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('core/email_template_collection');

        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
            	  'header'=>__('ID'), 
            	  'index'=>'template_id'
            )
        );
        
        $this->addColumn('code',
            array(
                'header'=>__('Template Name'),
                'index'=>'template_code'
        ));
        
        $this->addColumn('added_at',
            array(
                'header'=>__('Date Added'),
                'index'=>'added_at',
                'type'=>'datetime'
        ));
        
        $this->addColumn('modified_at',
            array(
                'header'=>__('Date Updated'),
                'index'=>'modified_at',
                'type'=>'datetime'
        ));
        
        $this->addColumn('subject',
            array(
                'header'=>__('Subject'),
                'index'=>'template_subject'
        ));
        
        $this->addColumn('sender',
            array(
                'header'=>__('Sender'),
                'index'=>'template_sender_email',
                'renderer' => 'adminhtml/system_email_template_grid_renderer_sender'
        ));
        
        $this->addColumn('type',
            array(
                'header'=>__('Template Type'),
                'index'=>'template_type',
                'filter' => 'adminhtml/system_email_template_grid_filter_type',
                'renderer' => 'adminhtml/system_email_template_grid_renderer_type'
        ));
        
        $this->addColumn('action',
            array(
                'header'	=> __('Action'),
                'index'		=> 'template_id',
                'sortable'  => false,
                'filter' 	=> false,
                'width'		=> '100px',
                'renderer'  => 'adminhtml/system_email_template_grid_renderer_action'
        ));
        
        return $this;
    }
    
    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('id'=>$row->getId()));
    }
}
