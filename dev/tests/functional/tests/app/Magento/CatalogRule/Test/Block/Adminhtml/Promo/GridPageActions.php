<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Block\Adminhtml\Promo;

use Magento\Backend\Test\Block\GridPageActions as AbstractPageActions;
use Mtf\Client\Element\Locator;

/**
 * Class GridPageActions
 * Grid page actions block for 'Catalog Price Rules'
 *
 */
class GridPageActions extends AbstractPageActions
{
    /**
     * 'Apply Rules' button
     *
     * @var string
     */
    protected $applyRules = '#apply_rules';

    /**
     * 'Add New' catalog rule button
     *
     * @var string
     */
    protected $addNewCatalogRule = "//*[@class='page-actions']//*[@id='add']";

    /**
     * Click 'Apply Rules' button
     */
    public function applyRules()
    {
        $this->_rootElement->find($this->applyRules)->click();
    }

    /**
     * Add new catalog rule
     *
     * @return void
     */
    public function addNewCatalogRule()
    {
        $this->_rootElement->find($this->addNewCatalogRule, Locator::SELECTOR_XPATH)->click();
    }
}
