<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class CheckoutOnepageSuccess
 * One page checkout success page
 */
class CheckoutOnepageSuccess extends FrontendPage
{
    /**
     * URL for checkout success page
     */
    const MCA = 'checkout/onepage/success';

    protected $_blocks = [
        'successBlock' => [
            'name' => 'successBlock',
            'class' => 'Magento\Checkout\Test\Block\Onepage\Success',
            'locator' => '#maincontent',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Checkout\Test\Block\Onepage\Success
     */
    public function getSuccessBlock()
    {
        return $this->getBlockInstance('successBlock');
    }
}
