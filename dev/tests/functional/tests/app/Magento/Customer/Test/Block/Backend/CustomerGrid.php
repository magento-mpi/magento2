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
     * Customer group selector by customer email
     *
     * @var string
     */
    protected $customerGroupSelector = '//tr[td[text()[normalize-space()="%s"]]]/td[normalize-space(@class)="col-group"]';

    /**
     * Initialize block elements  contains
     */
    protected function _init()
    {
        parent::_init();
        $this->editLink = '//td[contains(@class, "col-action")]//a';
        $this->filters = array(
            'email' => array(
                'selector' => '#customerGrid_filter_email'
            ),
        );
    }

    /**
     * Get Group name by email
     *
     * @param $email
     * @return string
     */
    public function getGroupByEmail($email)
    {
        $group = $this->_rootElement->find(sprintf($this->customerGroupSelector, $email), Locator::SELECTOR_XPATH);
        return $group->getText();
    }
}
