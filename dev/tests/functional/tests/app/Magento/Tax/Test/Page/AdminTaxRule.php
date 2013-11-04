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

namespace Magento\Tax\Test\Page;

use \Magento\Backend\Test\Block\Tax\Rule;
use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\PageActions;

/**
 * Class Login.
 * Customer frontend login page.
 *
 * @package Magento\Customer\Test\Page
 */
class AdminTaxRule extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'admin/tax_rule/';

    /**
     * @var PageActions
     */
    private $actionsBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->actionsBlock = Factory::getBlockFactory()->getMagentoBackendPageActions(
            $this->_browser->find('.page-actions')
        );
    }

    /**
     * Get
     *
     * @return \Magento\Backend\Test\Block\Tax\Rule
     */
    public function getRuleGrid()
    {
        return Factory::getBlockFactory()->getMagentoBackendTaxRule(
            $this->_browser->find('taxRuleGrid', Locator::SELECTOR_ID)
        );
    }

    /**
     * @return \Magento\Backend\Test\Block\PageActions
     */
    public function getActionsBlock()
    {
        return $this->actionsBlock;
    }
}
