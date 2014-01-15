<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for Catalog Category URL rewrites
 *
 * @method \Magento\Catalog\Model\Category getCategory()
 * @method \Magento\Backend\Block\Urlrewrite\Catalog\Category\Edit
 *    setCategory(\Magento\Catalog\Model\Category $category)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Urlrewrite\Catalog\Category;

class Edit
    extends \Magento\Backend\Block\Urlrewrite\Edit
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url\RewriteFactory $rewriteFactory
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url\RewriteFactory $rewriteFactory,
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = array()
    ) {
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $rewriteFactory, $adminhtmlData, $data);
    }

    /**
     * Prepare layout for URL rewrite creating for category
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
     */
    private function _addCategoryLinkBlock()
    {
        $this->addChild('category_link', 'Magento\Backend\Block\Urlrewrite\Link', array(
            'item_url'  => $this->_adminhtmlData->getUrl('adminhtml/*/*') . 'category',
            'item_name' => $this->_getCategory()->getName(),
            'label'     => __('Category:')
        ));
    }

    /**
     * Add child category tree block
     */
    private function _addCategoryTreeBlock()
    {
        $this->addChild('categories_tree', 'Magento\Backend\Block\Urlrewrite\Catalog\Category\Tree');
    }

    /**
     * Creates edit form block
     *
     * @return \Magento\Backend\Block\Urlrewrite\Catalog\Edit\Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('Magento\Backend\Block\Urlrewrite\Catalog\Edit\Form', '', array(
            'data' => array(
                'category'    => $this->_getCategory(),
                'url_rewrite' => $this->_getUrlRewrite()
            )
        ));
    }
}
