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
 * Class LogEntry
 */
class LogEntry extends BackendPage
{
    const MCA = '';

    protected $_blocks = [
        'details' => [
            'name' => 'details',
            'class' => 'Magento\Logging\Test\Block\Adminhtml\Details',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Logging\Test\Block\Adminhtml\Details
     */
    public function getDetails()
    {
        return $this->getBlockInstance('details');
    }
}
