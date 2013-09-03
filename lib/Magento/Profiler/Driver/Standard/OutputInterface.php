<?php
/**
 * Interface for output class of standard profiler driver.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Profiler\Driver\Standard;

interface OutputInterface
{
    /**
     * Display profiling results in appropriate format
     *
     * @param \Magento\Profiler\Driver\Standard\Stat $stat
     */
    public function display(\Magento\Profiler\Driver\Standard\Stat $stat);
}
