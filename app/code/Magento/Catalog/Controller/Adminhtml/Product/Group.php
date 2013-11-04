<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Controller\Adminhtml\Product;

class Group extends \Magento\Backend\Controller\Adminhtml\Action
{
    public function saveAction()
    {
        $model = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Group');

        $model->setAttributeGroupName($this->getRequest()->getParam('attribute_group_name'))
              ->setAttributeSetId($this->getRequest()->getParam('attribute_set_id'));

        if( $model->itemExists() ) {
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__('A group with the same name already exists.'));
        } else {
            try {
                $model->save();
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__('Something went wrong while saving this group.'));
            }
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::products');
    }
}
