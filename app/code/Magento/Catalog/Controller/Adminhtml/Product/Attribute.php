<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product attribute controller
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class Attribute extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    protected $_attributeLabelCache;

    /**
     * @var string
     */
    protected $_entityTypeId;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Cache\FrontendInterface $attributeLabelCache
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Cache\FrontendInterface $attributeLabelCache,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_attributeLabelCache = $attributeLabelCache;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $this->_entityTypeId = $this->_objectManager->create(
            'Magento\Eav\Model\Entity'
        )->setType(
            \Magento\Catalog\Model\Product::ENTITY
        )->getTypeId();
        return parent::dispatch($request);
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_title->add(__('Product Attributes'));

        if ($this->getRequest()->getParam('popup')) {
            if ($this->getRequest()->getParam('product_tab') == 'variations') {
                $this->_view->loadLayout(
                    array('popup', 'catalog_product_attribute_edit_product_tab_variations_popup')
                );
            } else {
                $this->_view->loadLayout(array('popup', 'catalog_product_attribute_edit_popup'));
            }
            /** @var \Magento\Framework\View\Page\Config $pageConfig */
            $pageConfig = $this->_objectManager->get('Magento\Framework\View\Page\Config');
            $pageConfig->addBodyClass('attribute-popup');
        } else {
            $this->_view->loadLayout();
            $this->_addBreadcrumb(
                __('Catalog'),
                __('Catalog')
            )->_addBreadcrumb(
                __('Manage Product Attributes'),
                __('Manage Product Attributes')
            );
            $this->_setActiveMenu('Magento_Catalog::catalog_attributes_attributes');
        }

        return $this;
    }

    /**
     * Generate code from label
     *
     * @param string $label
     * @return string
     */
    protected function generateCode($label)
    {
        $code = substr(
            preg_replace(
                '/[^a-z_0-9]/',
                '_',
                $this->_objectManager->create('Magento\Catalog\Model\Product\Url')->formatUrlKey($label)
            ),
            0,
            30
        );
        $validatorAttrCode = new \Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/'));
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }
        return $code;
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::attributes_attributes');
    }
}
