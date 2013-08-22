<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter queue grid block action item renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Newsletter_Queue_Grid_Renderer_Action extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Magento_Object $row)
    {
        $actions = array();

        if($row->getQueueStatus()==Magento_Newsletter_Model_Queue::STATUS_NEVER) {
               if(!$row->getQueueStartAt() && $row->getSubscribersTotal()) {
                $actions[] = array(
                    'url' => $this->getUrl('*/*/start', array('id'=>$row->getId())),
                    'caption'	=> __('Start')
                );
            }
        } else if ($row->getQueueStatus()==Magento_Newsletter_Model_Queue::STATUS_SENDING) {
            $actions[] = array(
                    'url' => $this->getUrl('*/*/pause', array('id'=>$row->getId())),
                    'caption'	=>	__('Pause')
            );

            $actions[] = array(
                'url'		=>	$this->getUrl('*/*/cancel', array('id'=>$row->getId())),
                'confirm'	=>	__('Do you really want to cancel the queue?'),
                'caption'	=>	__('Cancel')
            );


        } else if ($row->getQueueStatus()==Magento_Newsletter_Model_Queue::STATUS_PAUSE) {

            $actions[] = array(
                'url' => $this->getUrl('*/*/resume', array('id'=>$row->getId())),
                'caption'	=>	__('Resume')
            );

        }

        $actions[] = array(
            'url'       =>  $this->getUrl('*/newsletter_queue/preview',array('id'=>$row->getId())),
            'caption'   =>  __('Preview'),
            'popup'     =>  true
        );

        $this->getColumn()->setActions($actions);
        return parent::render($row);
    }
}
