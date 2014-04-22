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
 * Class CustomerAccountLogin
 * Customer frontend login page.
 *
 * @package Magento\Customer\Test\Page
 */
class CustomerAccountLogin extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'customer/account/login';

    /**
     * Messages block
     *
     * @var string
     */
    protected $messagesBlock = '.page.messages';

    /**
     * Form for customer login
     *
     * @var string
     */
    protected $loginBlock = '#login-form';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get Messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessages()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages($this->_browser->find($this->messagesBlock));
    }

    /**
     * Get customer login form
     *
     * @return \Magento\Customer\Test\Block\Form\Login
     */
    public function getLoginBlock()
    {
        return Factory::getBlockFactory()->getMagentoCustomerFormLogin(
            $this->_browser->find($this->loginBlock, Locator::SELECTOR_CSS)
        );
    }
}
