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

namespace Magento\Customer\Test\Block\Backend;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class CustomerGrid
 * Backend customer grid
 *
 * @package Magento\Customer\Test\Block\Backend
 */
class CustomerGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'email' => array(
                'selector' => '#customerGrid_filter_email'
            ),
        );
    }

    /**
     * Click "Select All" and submit Delete action
     */
    public function deleteAll()
    {
        $this->_rootElement
            ->find('//*[@id="customerGrid_massaction"]//a[text()="Select All"]', Locator::SELECTOR_XPATH)
            ->click();
        $this->_rootElement
            ->find('customerGrid_massaction-select', Locator::SELECTOR_ID, 'select')
            ->setValue('Delete');
        $this->_rootElement
            ->find('customerGrid_massaction-form', Locator::SELECTOR_ID)
            ->find('[title=Submit]', Locator::SELECTOR_CSS)
            ->click();
        $this->_rootElement->acceptAlert();
    }

    /**
     * Click create new customer button
     */
    public function createNewCustomer()
    {
        $this->_rootElement->find('#add')->click();
    }
}
