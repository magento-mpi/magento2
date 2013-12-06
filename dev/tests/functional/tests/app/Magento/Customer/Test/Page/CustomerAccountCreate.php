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

namespace Magento\Customer\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Customer Create page on frontend
 *
 * @package Magento\Customer\Test\Page
 */
class CustomerAccountCreate extends Page
{
    /**
     * URL for customer Create
     */
    const MCA = 'customer/account/create';

    /**
     * Customer Create form
     *
     * @var string
     */
    protected $createForm = '#form-validate';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get Customer Create form
     *
     * @return \Magento\Customer\Test\Block\Form\Create
     */
    public function getCreateForm()
    {
        return Factory::getBlockFactory()->getMagentoCustomerFormCreate(
            $this->_browser->find($this->createForm, Locator::SELECTOR_CSS)
        );
    }
}
