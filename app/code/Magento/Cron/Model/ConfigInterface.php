<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
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
