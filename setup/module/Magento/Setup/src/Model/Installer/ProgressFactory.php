<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model\Installer;

/**
 * Factory for progress indicator model
 */
class ProgressFactory
{
    /**
     * Creates a progress indicator
     *
     * @param int[] $quantities
     * @param int $current
     * @return Progress
     */
    public function create($quantities, $current = 0)
    {
        return new Progress(array_sum($quantities), $current);
    }

    /**
     * Creates a progress indicator from log contents
     *
     * @param string $contents
     * @param string $pattern
     * @return Progress
     */
    public function createFromLog($contents, $pattern)
    {
        $total = 1;
        $current = 0;
        if (preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER)) {
            $last = array_pop($matches);
            list(, $current, $total) = $last;
        }
        $progress = new Progress($total, $current);
        return $progress;
    }
}
