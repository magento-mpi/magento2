<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class BannerNew
 *
 * @package Magento\CustomerSegment\Test\Page\Adminhtml
 */
class BannerNew extends BackendPage
{
    const MCA = 'admin/banner/new/index';

    protected $_blocks = [
        'bannerForm' => [
            'name' => 'bannerForm',
            'class' => 'Magento\CustomerSegment\Test\Block\Adminhtml\Banner\BannerForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CustomerSegment\Test\Block\Adminhtml\Banner\BannerForm
     */
    public function getBannerForm()
    {
        return $this->getBlockInstance('bannerForm');
    }
}
