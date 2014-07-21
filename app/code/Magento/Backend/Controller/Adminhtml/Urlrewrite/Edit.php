<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Urlrewrite;

class Edit extends \Magento\Backend\Controller\Adminhtml\Urlrewrite
{
    const ID_MODE = 'id';

    const PRODUCT_MODE = 'product';

    const CATEGORY_MODE = 'category';

    const CMS_PAGE_MODE = 'cms_page';

    /**
     * Get current mode
     *
     * @return string
     */
    protected function _getMode()
    {
        if ($this->_getProduct()->getId() || $this->getRequest()->has('product')) {
            $mode = self::PRODUCT_MODE;
        } elseif ($this->_getCategory()->getId() || $this->getRequest()->has('category')) {
            $mode = self::CATEGORY_MODE;
        } elseif ($this->_getCmsPage()->getId() || $this->getRequest()->has('cms_page')) {
            $mode = self::CMS_PAGE_MODE;
        } elseif ($this->getRequest()->has('id')) {
            $mode = self::ID_MODE;
        } else {
            $mode = $this->_objectManager->get('Magento\Backend\Block\Urlrewrite\Selector')->getDefaultMode();
        }
        return $mode;
    }

    /**
     * Show urlrewrite edit/create page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('URL Redirects'));
        $this->_title->add(__('[New/Edit] URL Redirect'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Catalog::catalog_urlrewrite');

        $mode = $this->_getMode();

        switch ($mode) {
            case self::PRODUCT_MODE:
                $editBlock = $this->_view->getLayout()->createBlock(
                    'Magento\Backend\Block\Urlrewrite\Catalog\Product\Edit',
                    '',
                    array(
                        'data' => array(
                            'category' => $this->_getCategory(),
                            'product' => $this->_getProduct(),
                            'is_category_mode' => $this->getRequest()->has('category'),
                            'url_rewrite' => $this->_getUrlRewrite()
                        )
                    )
                );
                break;
            case self::CATEGORY_MODE:
                $editBlock = $this->_view->getLayout()->createBlock(
                    'Magento\Backend\Block\Urlrewrite\Catalog\Category\Edit',
                    '',
                    array(
                        'data' => array('category' => $this->_getCategory(), 'url_rewrite' => $this->_getUrlRewrite())
                    )
                );
                break;
            case self::CMS_PAGE_MODE:
                $editBlock = $this->_view->getLayout()->createBlock(
                    'Magento\Backend\Block\Urlrewrite\Cms\Page\Edit',
                    '',
                    array(
                        'data' => array('cms_page' => $this->_getCmsPage(), 'url_rewrite' => $this->_getUrlRewrite())
                    )
                );
                break;
            case self::ID_MODE:
            default:
                $editBlock = $this->_view->getLayout()->createBlock(
                    'Magento\Backend\Block\Urlrewrite\Edit',
                    '',
                    array('data' => array('url_rewrite' => $this->_getUrlRewrite()))
                );
                break;
        }

        $this->_addContent($editBlock);
        if (in_array($mode, array(self::PRODUCT_MODE, self::CATEGORY_MODE))) {
            $this->_view->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        }
        $this->_view->renderLayout();
    }
}
