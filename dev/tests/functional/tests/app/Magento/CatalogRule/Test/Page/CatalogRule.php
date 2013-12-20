<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Page;

use Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog;
use Magento\Core\Test\Block\Messages;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class CatalogRule
 * CatalogRule grid page.
 *
 * @package Magento\CatalogRule\Test\Page
 */
class CatalogRule extends Page
{
    /**
     * URL for catalog price rules grid
     */
    const MCA = 'catalog_rule/promo_catalog';

    /**
     * Messages block selector
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Catalog price rule grid block selector
     */
    protected $catalogRuleGrid = '#promo_catalog_grid';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get catalog price rule grid block
     *
     * @return Catalog
     */
    public function getCatalogPriceRuleGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogRuleAdminhtmlPromoCatalog(
            $this->_browser->find($this->catalogRuleGrid)
        );
    }

    /**
     * Get messages block
     *
     * @return Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesBlock)
        );
    }
}
