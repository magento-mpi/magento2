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

use \Magento\Backend\Test\Block\Tax\Rule\Edit;
use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class for new tax rule page
 *
 * @package Magento\Tax\Test\Page
 */
class AdminTaxRuleNew extends Page
{
    /**
     * URL for new tax rule
     */
    const MCA = 'admin/tax_rule/new/';

    /**
     * @var Edit
     */
    private $editBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->editBlock = Factory::getBlockFactory()->getMagentoBackendTaxRuleEdit(
            $this->_browser->find('[id="page:main-container"]', Locator::SELECTOR_CSS));
    }

    /**
     * @return \Magento\Backend\Test\Block\Tax\Rule\Edit
     */
    public function getEditBlock()
    {
        return $this->editBlock;
    }
}
