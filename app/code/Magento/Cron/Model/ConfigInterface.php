<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cron\Model;

/**
 * Interface \Magento\Cron\Model\ConfigInterface
 */
interface ConfigInterface
{
    /**
     * Return list of cron jobs
     *
     * @return array
     */
    public function getJobs();
}
