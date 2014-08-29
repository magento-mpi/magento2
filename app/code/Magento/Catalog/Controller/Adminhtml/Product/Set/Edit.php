<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Set;

class Edit extends \Magento\Catalog\Controller\Adminhtml\Product\Set
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Product Templates'));

        $this->_setTypeId();
        $attributeSet = $this->_objectManager->create(
            'Magento\Eav\Model\Entity\Attribute\Set'
        )->load(
            $this->getRequest()->getParam('id')
        );

        if (!$attributeSet->getId()) {
            $this->_redirect('catalog/*/index');
            return;
        }

        $this->_title->add($attributeSet->getId() ? $attributeSet->getAttributeSetName() : __('New Set'));

        $this->_coreRegistry->register('current_attribute_set', $attributeSet);

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Catalog::catalog_attributes_sets');
        $this->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_addBreadcrumb(__('Manage Product Sets'), __('Manage Product Sets'));

        $this->_view->renderLayout();
    }
}
