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

namespace Magento\Rma\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Search
 * Orders and returns search page
 *
 * @package Magento\Rma\Test\Page
 */
class Search extends Page
{
    /**
     * URL for orders and returns search page
     */
    const MCA = 'sales/guest/form';

    /**
     * Orders and Returns search form
     *
     * @var \Magento\Rma\Test\Block\Form\Search
     */
    protected $searchForm;

    /**
     * Form wrapper selector
     *
     * @var string
     */
    private $formWrapperSelector = '.form.orders.search';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA;
        $this->searchForm = Factory::getBlockFactory()->getMagentoRmaFormSearch(
            $this->_browser->find($this->formWrapperSelector, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get search block form
     *
     * @return \Magento\Rma\Test\Block\Form\Search
     */
    public function getSearchForm()
    {
        return $this->searchForm;
    }
}