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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Website delete page
 *
 * @author     Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_System_Website_Delete extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        $this->setTemplate('system/website/delete.phtml');

        $this->setChild('confirm_deletion_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('I\'m sure. Delete Website'),
                    'onclick'   => "deleteForm.submit()",
                    'class'     => 'delete'
                    ))
                );

        $this->setChild('cancel_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('I\'m not sure. Cancel'),
                    'onclick'   => "setLocation('".Mage::getUrl('*/system_config/edit', array('website' => $this->getRequest()->getParam('website')))."')",
                    'class'     => 'cancel'
                    ))
                );

        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Back'),
                    'onclick'   => "setLocation('".Mage::getUrl('*/system_config/edit', array('website' => $this->getRequest()->getParam('website')))."')",
                    'class'     => 'back'
                    ))
                );

        return parent::_prepareLayout();
    }
}