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

/**
 * Backend product review page
 *
 * @package Magento\Catalog\Test\Page\Product
 */
class CatalogProductReview extends Page
{
    /**
     * URL for catalog product review
     */
    const MCA = 'catalog/product_review';

    /**
     * Review grid selector
     *
     * @var string
     */
    protected $gridSelector = '#reviwGrid';

    /**
     * Edit form review selector
     *
     * @var string
     */
    protected $editFormSelector = '#anchor-content';

    /**
     * Messages selector
     *
     * @var string
     */
    protected $messageWrapperSelector = '#messages';

    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get product reviews grid
     *
     * @return \Magento\Backend\Test\Block\Review\Grid
     */
    public function getGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendReviewGrid($this->_browser->find($this->gridSelector));
    }

    /**
     * Get review edit form
     *
     * @return \Magento\Backend\Test\Block\Review\Edit
     */
    public function getEditForm()
    {
        return Factory::getBlockFactory()->getMagentoBackendReviewEdit($this->_browser->find($this->editFormSelector));
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages($this->_browser->find($this->messageWrapperSelector));
    }
}
