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
 * Class CustomerAccountEdit
 */
class CustomerAccountEdit extends FrontendPage
{
    const MCA = 'customer/account/edit';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'accountInfoForm' => [
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
