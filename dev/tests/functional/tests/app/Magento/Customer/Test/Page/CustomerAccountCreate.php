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
use Magento\Customer\Test\Block\Form;
use Magento\Customer\Test\Block\Form\Create;

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
     * @var Create
     */
    private $createForm;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;

        $this->createForm = Factory::getBlockFactory()->getMagentoCustomerFormCreate(
            $this->_browser->find('form-validate', Locator::SELECTOR_ID)
        );
    }

    /**
     * Get Customer Create form
     *
     * @return Create
     */
    public function getCreateForm()
    {
        return $this->createForm;
    }
}
