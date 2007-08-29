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

class Mage_Adminhtml_Block_Permissions_Buttons extends Mage_Core_Block_Template
{
    protected function _initChildren()
    {
        $this->setUserId($this->getRequest()->getParam('id'));
        $this->setRoleId($this->getRequest()->getParam('rid'));

        $this->setChild('backButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Back'),
                    'onclick'   => 'window.location.href=\''.Mage::getUrl('*/*/').'\'',
					'class' => 'back'
                ))
        );

        $this->setChild('resetButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Reset'),
                    'onclick'   => 'window.location.reload()',
                    'class'     => 'reset',
                ))
        );

        $this->setChild('saveButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save User'),
                    'onclick'   => 'userForm.submit();',
                    'class'     => 'save',
                ))
        );

        $this->setChild('deleteButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete User'),
                    'onclick'   => 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\' ,\'' . Mage::getUrl('*/*/deleteUser', array('id'=>$this->getUserId())) . '\')',
                    'class'     => 'delete',
                ))
        );

        $this->setChild('saveRoleButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save Role'),
                    'onclick'   => 'roleForm.submit();',
                    'class'     => 'save',
                ))
        );

        $this->setChild('deleteRoleButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete Role'),
                    'onclick'   => 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\' ,\'' . Mage::getUrl('*/*/deleteRole', array('id'=>$this->getRoleId())) . '\')',
                    'class'     => 'delete',
                ))
        );
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    public function getDeleteButtonHtml()
    {
        if( $this->getUserId() ) {
            return $this->getChildHtml('deleteButton');
        } else {
        	return false;
        }
    }

    public function getSaveRoleButtonHtml()
    {
        return $this->getChildHtml('saveRoleButton');
    }

    public function getDeleteRoleButtonHtml()
    {
        if( $this->getRoleId() ) {
            return $this->getChildHtml('deleteRoleButton');
        } else {
        	return false;
        }
    }
}