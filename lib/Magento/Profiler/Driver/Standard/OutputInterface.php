<?php
/**
 * Interface for output class of standard profiler driver.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Profiler_Driver_Standard_OutputInterface
{
    /**
     * Display profiling results in appropriate format
     *
     * @param Magento_Profiler_Driver_Standard_Stat $stat
     */
    public function display(Magento_Profiler_Driver_Standard_Stat $stat);
}
