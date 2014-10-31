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
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_title->add(__('Product Templates'));

        $this->_setTypeId();
        $attributeSet = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
            ->load($this->getRequest()->getParam('id'));

        if (!$attributeSet->getId()) {
            return $this->resultRedirectFactory->create()->setPath('catalog/*/index');
        }

        $this->_title->add($attributeSet->getId() ? $attributeSet->getAttributeSetName() : __('New Set'));

        $this->_coreRegistry->register('current_attribute_set', $attributeSet);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Catalog::catalog_attributes_sets');
        $resultPage->addBreadcrumb(__('Catalog'), __('Catalog'));
        $resultPage->addBreadcrumb(__('Manage Product Sets'), __('Manage Product Sets'));
        return $resultPage;
    }
}
