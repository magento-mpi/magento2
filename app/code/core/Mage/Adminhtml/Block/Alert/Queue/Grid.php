<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml alert queue grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Vasily Selivanov <vasily@varien.com>
 */
class Mage_Adminhtml_Block_Alert_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $collection = Mage::getResourceModel('customeralert/queue_collection')
            ->addTemplateInfo()
            ->addSubscribersInfo();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    =>  __('ID'),
            'index'     =>  'queue_id',
            'width'     =>  10
        ));

        $this->addColumn('start_at', array(
            'header'    =>  __('Queue Start'),
            'type'      =>  'datetime',
            'index'     =>  'queue_start_at',
            'default'   =>  ' ---- '
        ));

        $this->addColumn('finish_at', array(
            'header'    =>  __('Queue Finish'),
            'type'      =>  'datetime',
            'index'     =>  'queue_finish_at',
            'default'   =>  ' ---- '
        ));

        $this->addColumn('template_subject', array(
            'header'    =>  __('Subject'),
            'index'     =>  'template_subject'
        ));

         $this->addColumn('status', array(
            'header'    =>  __('Status'),
            'index'     => 'queue_status',
            'type'      => 'options',
            'options'   => array(
                Mage_CustomerAlert_Model_Queue::STATUS_SENT    => __('Sent'),
                Mage_CustomerAlert_Model_Queue::STATUS_CANCEL  => __('Cancelled'),
                Mage_CustomerAlert_Model_Queue::STATUS_NEVER   => __('Not Sent'),
                Mage_CustomerAlert_Model_Queue::STATUS_SENDING => __('Sending'),
                Mage_CustomerAlert_Model_Queue::STATUS_PAUSE   => __('Paused'),
            ),
            'width'     => '100px',
        ));

        $this->addColumn('subscribers_sent', array(
            'header'    =>  __('Processed'),
            'type'      => 'number',
            'index'     => 'subscribers_sent'
        ));

        $this->addColumn('subscribers_total', array(
            'header'    =>  __('Recipients'),
            'type'      => 'number',
            'index'     => 'subscribers_total'
        ));



        $this->addColumn('action', array(
            'header'    =>  __('Action'),
            'filter'    =>  false,
            'sortable'  =>  false,
            'width'     => '100px',
            'renderer'  =>  'adminhtml/alert_queue_grid_renderer_action'
        ));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('id'=>$row->getId()));
    }
}
