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

namespace Magento\CatalogRule\Test\Page;

use Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog\Edit;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class CatalogRuleNew
 * CatalogRule creation page.
 *
 * @package Magento\CatalogRule\Test\Page
 */
class CatalogRuleNew extends Page
{
    /**
     * URL for creating catalog price rule
     */
    const MCA = 'catalog_rule/promo_catalog/new';

    /**
     * @var string
     */
    protected $formSelector = 'page:main-container';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get the general information block
     *
     * @return Edit
     */
    public function getCatalogPriceRuleForm()
    {
        return Factory::getBlockFactory()->getMagentoCatalogRuleAdminhtmlPromoCatalogEdit(
            $this->_browser->find($this->formSelector, Locator::SELECTOR_ID)
        );
    }
}
