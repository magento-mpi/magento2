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
 * Class Report
 */
class Report extends BackendPage
{
    const MCA = 'admin/logging';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Logging\Test\Block\LogGrid',
            'locator' => '#loggingLogGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Logging\Test\Block\LogGrid
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }
}
