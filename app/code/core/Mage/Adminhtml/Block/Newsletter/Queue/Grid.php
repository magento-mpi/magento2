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
class Mage_Adminhtml_Block_Newsletter_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('queueGrid');
        $this->setDefaultSort('start_at');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('newsletter/queue_collection')
			->addTemplateInfo()
			->addSubscribersInfo();
			
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
            'header'    =>	__('Queue Start'),
            'type'      =>	'date',
            'align'     =>	'center',
            'index'     =>	'queue_start_at',
            'format'	=>	'%D %T',
            'default'	=> 	' ---- '
        ));
        
        $this->addColumn('finish_at', array(
            'header'    =>	__('Queue Finish'),
            'type'      => 	'date',
            'align'     => 	'center',
            'index'     =>	'queue_finish_at',
            'format'	=>	'%D %T',
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
            'filter'	=>	'adminhtml/newsletter_queue_grid_filter_status',
            'index'		=> 'queue_status',
            'sortable'	=>	false,
            'renderer'	=>	'adminhtml/newsletter_queue_grid_renderer_status'
        ));
        
        $this->addColumn('subscribers_sent', array(
            'header'    =>	__('Sent'),
            'align'     =>	'center',
            'filter'	=> false,
            'index'		=> 'subscribers_sent',
            'format'	=> '$subscribers_sent / $subscribers_total'
        ));
        
        $this->addColumn('action', array(
            'header'    =>	__('Action'),
            'align'     =>	'center',
            'filter'	=>	false,
            'sortable'	=>	false,
            'renderer'	=>	'adminhtml/newsletter_queue_grid_renderer_action'
        ));
                
        
        return parent::_prepareColumns();
    }

}
