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

class Group extends \Magento\Backend\App\Action
{
    public function saveAction()
    {
        $model = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Group');

        $model->setAttributeGroupName($this->getRequest()->getParam('attribute_group_name'))
              ->setAttributeSetId($this->getRequest()->getParam('attribute_set_id'));

        if ($model->itemExists()) {
            $this->messageManager->addError(__('A group with the same name already exists.'));
        } else {
            try {
                $model->save();
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving this group.'));
            }
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::products');
    }
}
