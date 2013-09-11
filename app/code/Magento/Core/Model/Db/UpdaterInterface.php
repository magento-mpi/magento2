<?php
/**
 * DB updater interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Db;

interface UpdaterInterface
{
    /**
     * if this node set to true, we will ignore Developer Mode for applying updates
     */
    const XML_PATH_IGNORE_DEV_MODE = 'global/skip_process_modules_updates_ignore_dev_mode';

    /**
     * if this node set to true, we will ignore applying scheme updates
     */
    const XML_PATH_SKIP_PROCESS_MODULES_UPDATES = 'global/skip_process_modules_updates';

    /**
     * Apply database scheme updates whenever needed
     */
    public function updateScheme();

    /**
     * Apply database data updates whenever needed
     */
    public function updateData();
}
