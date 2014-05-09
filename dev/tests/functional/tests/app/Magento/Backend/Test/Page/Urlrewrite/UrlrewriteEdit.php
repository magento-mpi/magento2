<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Page\Urlrewrite;

use Mtf\Page\Page,
    Mtf\Factory\Factory;

/**
 * Class UrlrewriteEdit
 * Backend URL rewrite edit/new page
 *
 */
class UrlrewriteEdit extends Page
{
    /**
     * URL for URL rewrite edit/new page
     */
    const MCA = 'admin/urlrewrite/edit';

    /**
     * Category tree block UI ID
     *
     * @var string
     */
    protected $categoryTreeBlock = '[data-ui-id="category-selector"]';

    /**
     * URL rewrite information form block UI ID
     *
     * @var string
     */
    protected $urlRewriteFormBlock = '[id="page:main-container"]';

    /**
     * Product grid block UI ID
     *
     * @var string
     */
    protected $productGridBlock = '[id="productGrid"]';

    /**
     * URL rewrite type selector block UI ID
     *
     * @var string
     */
    protected $typeSelectorBlock = '[data-ui-id="urlrewrite-type-selector"]';

    /**
     * @var string
     */
    protected $pageActionsBlock = '.page-main-actions';

    /**
     * Init page. Set page URL.
     */
    protected function _init()
    {
        parent::_init();
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Retrieve category tree block
     *
     * @return \Magento\Backend\Test\Block\Urlrewrite\Catalog\Category\Tree
     */
    public function getCategoryTreeBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendUrlrewriteCatalogCategoryTree(
            $this->_browser->find($this->categoryTreeBlock)
        );
    }

    /**
     * Retrieve URL rewrite information form block
     *
     * @return \Magento\Backend\Test\Block\Urlrewrite\Catalog\Edit\Form
     */
    public function getUrlRewriteInformationForm()
    {
        return Factory::getBlockFactory()->getMagentoBackendUrlrewriteCatalogEditForm(
            $this->_browser->find($this->urlRewriteFormBlock)
        );
    }

    /**
     * Retrieve product grid block
     *
     * @return \Magento\Backend\Test\Block\Urlrewrite\Catalog\Product\Grid
     */
    public function getProductGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendUrlrewriteCatalogProductGrid(
            $this->_browser->find($this->productGridBlock)
        );
    }

    /**
     * Retrieve URL rewrite type selector block
     *
     * @return \Magento\Backend\Test\Block\Urlrewrite\Selector
     */
    public function getUrlRewriteTypeSelectorBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendUrlrewriteSelector(
            $this->_browser->find($this->typeSelectorBlock)
        );
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendFormPageActions(
            $this->_browser->find($this->pageActionsBlock)
        );
    }
}
