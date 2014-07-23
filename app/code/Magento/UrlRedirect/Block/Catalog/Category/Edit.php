<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Block\Catalog\Category;

/**
 * Block for Catalog Category URL rewrites
 */
class Edit extends \Magento\UrlRedirect\Block\Edit
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\UrlRedirect\Model\UrlRedirectFactory $rewriteFactory
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\UrlRedirect\Model\UrlRedirectFactory $rewriteFactory,
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = array()
    ) {
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $rewriteFactory, $adminhtmlData, $data);
    }

    /**
     * Prepare layout for URL rewrite creating for category
     *
     * @return void
     */
    protected function _prepareLayoutFeatures()
    {
        if ($this->_getUrlRewrite()->getId()) {
            $this->_headerText = __('Edit URL Rewrite for a Category');
        } else {
            $this->_headerText = __('Add URL Rewrite for a Category');
        }

        if ($this->_getCategory()->getId()) {
            $this->_addCategoryLinkBlock();
            $this->_addEditFormBlock();
            $this->_updateBackButtonLink($this->_adminhtmlData->getUrl('adminhtml/*/edit') . 'category');
        } else {
            $this->_addUrlRewriteSelectorBlock();
            $this->_addCategoryTreeBlock();
        }
    }

    /**
     * Get or create new instance of category
     *
     * @return \Magento\Catalog\Model\Product
     */
    private function _getCategory()
    {
        if (!$this->hasData('category')) {
            $this->setCategory($this->_categoryFactory->create());
        }
        return $this->getCategory();
    }

    /**
     * Add child category link block
     *
     * @return void
     */
    private function _addCategoryLinkBlock()
    {
        $this->addChild(
            'category_link',
            'Magento\UrlRedirect\Block\Link',
            array(
                'item_url' => $this->_adminhtmlData->getUrl('adminhtml/*/*') . 'category',
                'item_name' => $this->_getCategory()->getName(),
                'label' => __('Category:')
            )
        );
    }

    /**
     * Add child category tree block
     *
     * @return void
     */
    private function _addCategoryTreeBlock()
    {
        $this->addChild('categories_tree', 'Magento\UrlRedirect\Block\Catalog\Category\Tree');
    }

    /**
     * Creates edit form block
     *
     * @return \Magento\UrlRedirect\Block\Catalog\Edit\Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock(
            'Magento\UrlRedirect\Block\Catalog\Edit\Form',
            '',
            array('data' => array('category' => $this->_getCategory(), 'url_rewrite' => $this->_getUrlRewrite()))
        );
    }
}
