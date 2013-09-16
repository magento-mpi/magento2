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
class Magento_Catalog_Block_Category_View extends Magento_Core_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->getLayout()->createBlock('Magento_Catalog_Block_Breadcrumbs');

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
            if ($this->helper('Magento_Catalog_Helper_Category')->canUseCanonicalTag()
                && !$headBlock->getChildBlock('magento-page-head-category-canonical-link')
            ) {
                $headBlock->addChild(
                    'magento-page-head-category-canonical-link',
                    'Magento_Page_Block_Html_Head_Link',
                    array(
                        'url' => $category->getUrl(),
                        'properties' => array('attributes' => array('rel' => 'canonical'))
                    )
                );
            }
            /**
             * want to show rss feed in the url
             */
            if ($this->IsRssCatalogEnable() && $this->IsTopCategory()) {
                $title = __('%1 RSS Feed',$this->getCurrentCategory()->getName());
                $headBlock->addRss($title, $this->getRssLink());
            }
            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle($this->getCurrentCategory()->getName());
            }
        }

        return $this;
    }

    public function IsRssCatalogEnable()
    {
        return Mage::getStoreConfig('rss/catalog/category');
    }

    public function IsTopCategory()
    {
        return $this->getCurrentCategory()->getLevel()==2;
    }

    public function getRssLink()
    {
        return Mage::getUrl('rss/catalog/category',array('cid' => $this->getCurrentCategory()->getId(), 'store_id' => Mage::app()->getStore()->getId()));
    }

    public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
    }

    /**
     * Retrieve current category model object
     *
     * @return Magento_Catalog_Model_Category
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
            $html = $this->getLayout()->createBlock('Magento_Cms_Block_Block')
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
        return $this->getCurrentCategory()->getDisplayMode() == Magento_Catalog_Model_Category::DM_PRODUCT;
    }

    /**
     * Check if category display mode is "Static Block and Products"
     * @return bool
     */
    public function isMixedMode()
    {
        return $this->getCurrentCategory()->getDisplayMode() == Magento_Catalog_Model_Category::DM_MIXED;
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
        if ($category->getDisplayMode()==Magento_Catalog_Model_Category::DM_PAGE) {
            $res = true;
            if ($category->getIsAnchor()) {
                $state = Mage::getSingleton('Magento_Catalog_Model_Layer')->getState();
                if ($state && $state->getFilters()) {
                    $res = false;
                }
            }
        }
        return $res;
    }
}
