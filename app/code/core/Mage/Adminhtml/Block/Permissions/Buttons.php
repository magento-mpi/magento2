<?php
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