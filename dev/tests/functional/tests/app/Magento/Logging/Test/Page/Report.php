<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Test\Page;

use Mtf\Factory\Factory;
use Mtf\Page\Page;
use Mtf\Client\Element\Locator;
use Magento\Logging\Test\Block\LogGrid;

/**
 * Class Report
 * Actions logging report
 *
 */
class Report extends Page
{
    /**
     * URL part for logging report page
     */
    const MCA = 'admin/logging/';

    /**
     * Grid with logs
     *
     * @var string LogGrid
     */
    protected $logGridBlock = 'loggingLogGrid';

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get log grid block
     *
     * @return LogGrid
     */
    public function getLogGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoLoggingLogGrid(
            $this->_browser->find($this->logGridBlock, Locator::SELECTOR_ID)
        );
    }
}
