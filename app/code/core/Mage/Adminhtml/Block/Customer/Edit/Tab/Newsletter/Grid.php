<?php
/**
 * Adminhtml newsletter queue grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('queueGrid');
        $this->setDefaultSort('start_at');
        $this->setDefaultDir('desc');
       
        $this->setUseAjax(true);
        
    
		$this->setEmptyText(__('No Newsletter Found'));
	        
    }
    
    public function getGridUrl()
    {
        return Mage::getUrl('*/*/newsletter', array('_current'=>true));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('newsletter/queue_collection')
			->addTemplateInfo()
			->addSubscriberFilter(Mage::registry('subscriber')->getId());
			
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    =>	__('ID'),
            'align'     =>	'left',
            'index'     =>	'queue_id',
            'width'		=>	10
        ));
        
        $this->addColumn('start_at', array(
            'header'    =>	__('Newsletter Start'),
            'type'      =>	'date',
            'align'     =>	'center',
            'index'     =>	'queue_start_at',
            'format'	=>	Mage::getStoreConfig('general/local/datetime_format_short'),
            'default'	=> 	' ---- '
        ));
        
        $this->addColumn('finish_at', array(
            'header'    =>	__('Newsletter Finish'),
            'type'      => 	'date',
            'align'     => 	'center',
            'index'     =>	'queue_finish_at',
            'format'	=>	Mage::getStoreConfig('general/local/datetime_format_short'),
            'default'	=> 	' ---- '
        ));
        
        $this->addColumn('letter_sent_at', array(
            'header'    =>	__('Newsletter Received'),
            'type'      => 	'date',
            'align'     => 	'center',
            'index'     =>	'letter_sent_at',
            'format'	=>	Mage::getStoreConfig('general/local/datetime_format_short'),
            'default'	=> 	' ---- '
        ));
        
        $this->addColumn('template_subject', array(
            'header'    =>	__('Subject'),
            'align'     =>	'center',
            'index'     =>	'template_subject'
        ));
        
         $this->addColumn('status', array(
            'header'    =>	__('Status'),
            'align'     =>	'center',
            'filter'	=>	'adminhtml/customer_edit_tab_newsletter_grid_filter_status',
            'index'		=> 'queue_status',
            'renderer'	=>	'adminhtml/customer_edit_tab_newsletter_grid_renderer_status'
        ));
        
        $this->addColumn('action', array(
            'header'    =>	__('Action'),
            'align'     =>	'center',
            'filter'	=>	false,
            'sortable'	=>	false,
            'renderer'	=>	'adminhtml/customer_edit_tab_newsletter_grid_renderer_action'
        ));
                
        
        return parent::_prepareColumns();
    }

}
