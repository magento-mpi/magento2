<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class Details
 */
class Details extends BackendPage
{
    const MCA = 'admin/logging/details';

    protected $_blocks = [
        'detailsBlock' => [
            'name' => 'detailsBlock',
            'class' => 'Magento\Logging\Test\Block\Adminhtml\Details',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Logging\Test\Block\Adminhtml\Details
     */
    public function getDetailsBlock()
    {
        return $this->getBlockInstance('detailsBlock');
    }
}
