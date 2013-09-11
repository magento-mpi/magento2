<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Controller\Catalog\Product;

class Group extends \Magento\Adminhtml\Controller\Action
{
    public function saveAction()
    {
        $model = \Mage::getModel('\Magento\Eav\Model\Entity\Attribute\Group');

        $model->setAttributeGroupName($this->getRequest()->getParam('attribute_group_name'))
              ->setAttributeSetId($this->getRequest()->getParam('attribute_set_id'));

        if( $model->itemExists() ) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('A group with the same name already exists.'));
        } else {
            try {
                $model->save();
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('Something went wrong while saving this group.'));
            }
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::products');
    }
}
