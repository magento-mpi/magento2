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
 */
class CheckoutOnepageSuccess extends FrontendPage
{
    const MCA = 'checkout/onepage/success';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'successBlock' => [
            'class' => 'Magento\Checkout\Test\Block\Onepage\Success',
            'locator' => '#maincontent',
            'strategy' => 'css selector',
        ],
        'titleBlock' => [
            'class' => 'Magento\Theme\Test\Block\Html\Title',
            'locator' => '[data-ui-id="page-title"]',
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

    /**
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return $this->getBlockInstance('titleBlock');
    }
}
