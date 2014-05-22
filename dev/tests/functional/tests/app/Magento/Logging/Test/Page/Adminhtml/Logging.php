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
 * Class Logging
 */
class Logging extends BackendPage
{
    const MCA = 'admin/logging';

    protected $_blocks = [
        'logGrid' => [
            'name' => 'logGrid',
            'class' => 'Magento\Logging\Test\Block\LogGrid',
            'locator' => '#loggingLogGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Logging\Test\Block\LogGrid
     */
    public function getLogGrid()
    {
        return $this->getBlockInstance('logGrid');
    }
}
