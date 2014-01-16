<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category View block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Category;

class View extends \Magento\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer $catalogLayer
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer $catalogLayer,
        \Magento\Core\Model\Registry $registry,
        \Magento\Catalog\Helper\Category $categoryHelper,
        array $data = array()
    ) {
        $this->_categoryHelper = $categoryHelper;
        $this->_catalogLayer = $catalogLayer;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->getLayout()->createBlock('Magento\Catalog\Block\Breadcrumbs');

        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $category = $this->getCurrentCategory();
            $title = $category->getMetaTitle();
            if ($title) {
                $headBlock->setTitle($title);
            }
            $description = $category->getMetaDescription();
            if ($description) {
                $headBlock->setDescription($description);
            }
            $keywords = $category->getMetaKeywords();
            if ($keywords) {
                $headBlock->setKeywords($keywords);
            }
            //@todo: move canonical link to separate block
            if ($this->_categoryHelper->canUseCanonicalTag()
                && !$headBlock->getChildBlock('magento-page-head-category-canonical-link')
            ) {
                $headBlock->addChild(
                    'magento-page-head-category-canonical-link',
                    'Magento\Theme\Block\Html\Head\Link',
                    array(
                        'url' => $category->getUrl(),
                        'properties' => array('attributes' => array('rel' => 'canonical'))
                    )
                );
            }
            /**
             * want to show rss feed in the url
             */
            if ($this->isRssCatalogEnable() && $this->isTopCategory()) {
                $title = __('%1 RSS Feed', $this->getCurrentCategory()->getName());
                $headBlock->addRss($title, $this->getRssLink());
            }
            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle($this->getCurrentCategory()->getName());
            }
        }

        return $this;
    }

    public function isRssCatalogEnable()
    {
        return $this->_storeConfig->getConfig('rss/catalog/category');
    }

    public function isTopCategory()
    {
        return $this->getCurrentCategory()->getLevel()==2;
    }

    public function getRssLink()
    {
        return $this->_urlBuilder->getUrl('rss/catalog/category', array(
            'cid' => $this->getCurrentCategory()->getId(),
            'store_id' => $this->_storeManager->getStore()->getId())
        );
    }

    public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
    }

    /**
     * Retrieve current category model object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', $this->_coreRegistry->registry('current_category'));
        }
        return $this->getData('current_category');
    }

    public function getCmsBlockHtml()
    {
        if (!$this->getData('cms_block_html')) {
            $html = $this->getLayout()->createBlock('Magento\Cms\Block\Block')
                ->setBlockId($this->getCurrentCategory()->getLandingPage())
                ->toHtml();
            $this->setData('cms_block_html', $html);
        }
        return $this->getData('cms_block_html');
    }

    /**
     * Check if category display mode is "Products Only"
     * @return bool
     */
    public function isProductMode()
    {
        return $this->getCurrentCategory()->getDisplayMode() == \Magento\Catalog\Model\Category::DM_PRODUCT;
    }

    /**
     * Check if category display mode is "Static Block and Products"
     * @return bool
     */
    public function isMixedMode()
    {
        return $this->getCurrentCategory()->getDisplayMode() == \Magento\Catalog\Model\Category::DM_MIXED;
    }

    /**
     * Check if category display mode is "Static Block Only"
     * For anchor category with applied filter Static Block Only mode not allowed
     *
     * @return bool
     */
    public function isContentMode()
    {
        $category = $this->getCurrentCategory();
        $res = false;
        if ($category->getDisplayMode() == \Magento\Catalog\Model\Category::DM_PAGE) {
            $res = true;
            if ($category->getIsAnchor()) {
                $state = $this->_catalogLayer->getState();
                if ($state && $state->getFilters()) {
                    $res = false;
                }
            }
        }
        return $res;
    }
}
