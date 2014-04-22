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
 * Class HomePage
 *
 * @package Magento\Customer\Test\Page
 */
class HomePage extends FrontendPage
{
    const MCA = 'cms/index/index';

    protected $_blocks = [
        'homePage' => [
            'name' => 'homePage',
            'class' => 'Magento\Theme\Test\Block\Html\Header',
            'locator' => '.panel.header',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Theme\Test\Block\Html\Header
     */
    public function getHomePage()
    {
        return $this->getBlockInstance('homePage');
    }
}
