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
 * Adminhtml newsletter queue grid block action item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Queue_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
    	$actions = array();


        if($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_NEVER) {
           	if(!$row->getQueueStartAt() && $row->getSubscribersTotal()) {
        		$actions[] = array(
		    		'url' => Mage::getUrl('*/*/start', array('id'=>$row->getId())),
		    		'caption'	=>	$this->__('Start')
		    	);
        	}
        } else if ($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_SENDING) {
        	$actions[] = array(
		    		'url' => Mage::getUrl('*/*/pause', array('id'=>$row->getId())),
		    		'caption'	=>	$this->__('Pause')
		    );

		    $actions[] = array(
    			'url'		=>	Mage::getUrl('*/*/cancel', array('id'=>$row->getId())),
    			'confirm'	=>	$this->__('Do you really want to cancel the queue?'),
    			'caption'	=>	$this->__('Cancel')
		    );


        } else if ($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_PAUSE) {

		    $actions[] = array(
    		    'url' => Mage::getUrl('*/*/resume', array('id'=>$row->getId())),
    		    'caption'	=>	$this->__('Resume')
		    );

        }

        $actions[] = array(
			'url'		=>	Mage::getUrl('*/newsletter_template/preview',array('id'=>$row->getTemplateId())),
			'caption'   =>	__('Preview'),
			'popup'	    =>	true
    	);

    	$this->getColumn()->setActions($actions);
        return parent::render($row);
    }

}
