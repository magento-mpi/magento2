<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Product;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class CatalogProductEdit
 * Edit product page
 */
class CatalogProductEdit extends Page
{
    /**
     * URL for product creation
     */
    const MCA = 'catalog/product/edit';

    /**
     * Messages block
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Product form block
     *
     * @var string
     */
    protected $productFormBlock = 'body';

    /**
     * Upsell block
     *
     * @var string
     */
    protected $upsellBlock = '#up_sell_product_grid';

    /**
     * Related block
     *
     * @var string
     */
    protected $relatedBlock = '#related_product_grid';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get product form block
     *
     * @return \Magento\Catalog\Test\Block\Backend\ProductForm
     */
    public function getProductBlockForm()
    {
        return Factory::getBlockFactory()->getMagentoCatalogBackendProductForm(
            $this->_browser->find($this->productFormBlock, Locator::SELECTOR_CSS)
        );
    }
  
    /**
     * Get upsell block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell
     */
    public function getUpsellBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabUpsell(
            $this->_browser->find($this->upsellBlock, Locator::SELECTOR_CSS)
        );
    }
 
    /**
     * Get related block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Related
     */
    public function getRelatedProductGrid()
    {
        return Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabRelated(
            $this->_browser->find($this->relatedBlock, Locator::SELECTOR_CSS)
        );
    }
}
