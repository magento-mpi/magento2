<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter queue grid block action item renderer
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Newsletter\Block\Adminhtml\Queue\Grid\Renderer;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    /**
     * Renders column
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $actions = array();

        if($row->getQueueStatus()==\Magento\Newsletter\Model\Queue::STATUS_NEVER) {
               if(!$row->getQueueStartAt() && $row->getSubscribersTotal()) {
                $actions[] = array(
                    'url' => $this->getUrl('*/*/start', array('id'=>$row->getId())),
                    'caption'	=> __('Start')
                );
            }
        } else if ($row->getQueueStatus()==\Magento\Newsletter\Model\Queue::STATUS_SENDING) {
            $actions[] = array(
                    'url' => $this->getUrl('*/*/pause', array('id'=>$row->getId())),
                    'caption'	=>	__('Pause')
            );

            $actions[] = array(
                'url'		=>	$this->getUrl('*/*/cancel', array('id'=>$row->getId())),
                'confirm'	=>	__('Do you really want to cancel the queue?'),
                'caption'	=>	__('Cancel')
            );


        } else if ($row->getQueueStatus()==\Magento\Newsletter\Model\Queue::STATUS_PAUSE) {

            $actions[] = array(
                'url' => $this->getUrl('*/*/resume', array('id'=>$row->getId())),
                'caption'	=>	__('Resume')
            );

        }

        $actions[] = array(
            'url'       =>  $this->getUrl('*/*/preview',array('id'=>$row->getId())),
            'caption'   =>  __('Preview'),
            'popup'     =>  true
        );

        $this->getColumn()->setActions($actions);
        return parent::render($row);
    }
}
