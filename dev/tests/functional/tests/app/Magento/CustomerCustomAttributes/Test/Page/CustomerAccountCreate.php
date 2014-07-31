<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class CustomerAccountCreate
 */
class CustomerAccountCreate extends FrontendPage
{
    const MCA = 'customer_account_create/customer/account/create';

    protected $_blocks = [
        'registerForm' => [
            'name' => 'registerForm',
            'class' => 'Magento\CustomerCustomAttributes\Test\Block\Form\Register',
            'locator' => '#form-validate',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CustomerCustomAttributes\Test\Block\Form\Register
     */
    public function getRegisterForm()
    {
        return $this->getBlockInstance('registerForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
