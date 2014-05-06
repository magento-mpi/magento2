<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class AdminAuth
 *
 * @package Magento\Backend\Test\Page
 */
class AdminAuth extends FrontendPage
{
    const MCA = 'backend/admin';

    protected $_blocks = [
        'loginForm' => [
            'name' => 'loginForm',
            'class' => 'Magento\Backend\Test\Block\Admin\LoginForm',
            'locator' => '#login-form',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\Admin\LoginForm
     */
    public function getLoginForm()
    {
        return $this->getBlockInstance('loginForm');
    }
}
