<?php
/**
 * DB updater interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Core_Model_Db_UpdaterInterface
{
    /**
     * Apply database scheme updates whenever needed
     */
    public function updateScheme();

    /**
     * Apply database data updates whenever needed
     */
    public function updateData();
}
