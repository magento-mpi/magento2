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
 * Class CustomerAccountEdit
 * Customer account info edit page
 */
class CustomerAccountEdit extends FrontendPage
{
    const MCA = 'customer_custom_attributes/customer/account/edit';

    protected $_blocks = [
        'accountInfoForm' => [
            'name' => 'accountInfoForm',
            'class' => 'Magento\CustomerCustomAttributes\Test\Block\Form\CustomerForm',
            'locator' => '#form-validate',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CustomerCustomAttributes\Test\Block\Form\CustomerForm
     */
    public function getAccountInfoForm()
    {
        return $this->getBlockInstance('accountInfoForm');
    }
}
