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
     * Apply database scheme updates whenever needed
     */
    public function updateScheme();

    /**
     * Apply database data updates whenever needed
     */
    public function updateData();
}
