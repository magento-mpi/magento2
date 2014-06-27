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
        'logGridBlock' => [
            'name' => 'logGridBlock',
            'class' => 'Magento\Logging\Test\Block\Grid',
            'locator' => '#loggingLogGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Logging\Test\Block\Grid
     */
    public function getLogGridBlock()
    {
        return $this->getBlockInstance('logGridBlock');
    }
}
