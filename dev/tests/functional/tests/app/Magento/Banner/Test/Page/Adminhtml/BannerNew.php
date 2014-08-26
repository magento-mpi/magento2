<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class BannerNew
 */
class BannerNew extends BackendPage
{
    const MCA = 'admin/banner/new/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'bannerForm' => [
            'class' => 'Magento\Banner\Test\Block\Adminhtml\Banner\BannerForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'pageMainActions' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'segmentBannerForm' => [
            'class' => 'Magento\CustomerSegment\Test\Block\Adminhtml\Banner\BannerForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Banner\Test\Block\Adminhtml\Banner\BannerForm
     */
    public function getBannerForm()
    {
        return $this->getBlockInstance('bannerForm');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageMainActions()
    {
        return $this->getBlockInstance('pageMainActions');
    }

    /**
     * @return \Magento\CustomerSegment\Test\Block\Adminhtml\Banner\BannerForm
     */
    public function getSegmentBannerForm()
    {
        return $this->getBlockInstance('segmentBannerForm');
    }
}
