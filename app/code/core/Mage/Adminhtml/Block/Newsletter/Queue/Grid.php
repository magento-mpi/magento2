<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter queue grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Newsletter_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('queueGrid');
        $this->setDefaultSort('start_at');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Newsletter_Model_Resource_Queue_Collection')
            ->addSubscribersInfo();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('queue_id', array(
            'header'    =>  Mage::helper('Mage_Newsletter_Helper_Data')->__('ID'),
            'index'     =>	'queue_id',
            'width'		=>	10
        ));

        $this->addColumn('start_at', array(
            'header'    =>  Mage::helper('Mage_Newsletter_Helper_Data')->__('Queue Start'),
            'type'      =>	'datetime',
            'index'     =>	'queue_start_at',
            'gmtoffset' => true,
            'default'	=> 	' ---- '
        ));

        $this->addColumn('finish_at', array(
            'header'    =>  Mage::helper('Mage_Newsletter_Helper_Data')->__('Queue Finish'),
            'type'      => 	'datetime',
            'index'     =>	'queue_finish_at',
            'gmtoffset' => true,
            'default'	=> 	' ---- '
        ));

        $this->addColumn('newsletter_subject', array(
            'header'    =>  Mage::helper('Mage_Newsletter_Helper_Data')->__('Subject'),
            'index'     =>  'newsletter_subject'
        ));

         $this->addColumn('status', array(
            'header'    => Mage::helper('Mage_Newsletter_Helper_Data')->__('Status'),
            'index'		=> 'queue_status',
            'type'      => 'options',
            'options'   => array(
                Mage_Newsletter_Model_Queue::STATUS_SENT 	=> Mage::helper('Mage_Newsletter_Helper_Data')->__('Sent'),
                Mage_Newsletter_Model_Queue::STATUS_CANCEL	=> Mage::helper('Mage_Newsletter_Helper_Data')->__('Cancelled'),
                Mage_Newsletter_Model_Queue::STATUS_NEVER 	=> Mage::helper('Mage_Newsletter_Helper_Data')->__('Not Sent'),
                Mage_Newsletter_Model_Queue::STATUS_SENDING => Mage::helper('Mage_Newsletter_Helper_Data')->__('Sending'),
                Mage_Newsletter_Model_Queue::STATUS_PAUSE 	=> Mage::helper('Mage_Newsletter_Helper_Data')->__('Paused'),
            ),
            'width'     => '100px',
        ));

        $this->addColumn('subscribers_sent', array(
            'header'    =>  Mage::helper('Mage_Newsletter_Helper_Data')->__('Processed'),
               'type'		=> 'number',
            'index'		=> 'subscribers_sent'
        ));

        $this->addColumn('subscribers_total', array(
            'header'    =>  Mage::helper('Mage_Newsletter_Helper_Data')->__('Recipients'),
            'type'		=> 'number',
            'index'		=> 'subscribers_total'
        ));

        $this->addColumn('action', array(
            'header'    =>  Mage::helper('Mage_Newsletter_Helper_Data')->__('Action'),
            'filter'	=>	false,
            'sortable'	=>	false,
            'no_link'   => true,
            'width'		=> '100px',
            'renderer'	=>	'Mage_Adminhtml_Block_Newsletter_Queue_Grid_Renderer_Action'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

}

