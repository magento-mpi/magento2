<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class CustomerAccountCreate
 */
class CustomerAccountCreate extends FrontendPage
{
    const MCA = 'customer/account/create';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'registerForm' => [
            'class' => 'Magento\Customer\Test\Block\Form\Register',
            'locator' => '#form-validate',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages',
            'strategy' => 'css selector',
        ],
        'customerAttributesRegisterForm' => [
            'class' => 'Magento\CustomerCustomAttributes\Test\Block\Form\Register',
            'locator' => '#form-validate',
            'strategy' => 'css selector',
        ],
        'tooltipBlock' => [
            'class' => 'Magento\Reward\Test\Block\Tooltip',
            'locator' => '.customer-form-before',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Customer\Test\Block\Form\Register
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

    /**
     * @return \Magento\CustomerCustomAttributes\Test\Block\Form\Register
     */
    public function getCustomerAttributesRegisterForm()
    {
        return $this->getBlockInstance('customerAttributesRegisterForm');
    }

    /**
     * @return \Magento\Reward\Test\Block\Tooltip
     */
    public function getTooltipBlock()
    {
        return $this->getBlockInstance('tooltipBlock');
    }
}
