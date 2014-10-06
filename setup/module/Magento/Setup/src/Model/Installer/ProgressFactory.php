<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model\Installer;

use Magento\Setup\Model\WebLogger;
use Magento\Setup\Model\Installer;

/**
 * Factory for progress indicator model
 */
class ProgressFactory
{
    /**
     * Creates a progress indicator from log contents
     *
     * @param WebLogger $logger
     * @return Progress
     */
    public function createFromLog(WebLogger $logger)
    {
        $total = 1;
        $current = 0;
        $contents = implode('', $logger->get());
        if (preg_match_all(Installer::PROGRESS_LOG_REGEX, $contents, $matches, PREG_SET_ORDER)) {
            $last = array_pop($matches);
            list(, $current, $total) = $last;
        }
        $progress = new Progress($total, $current);
        return $progress;
    }
}
