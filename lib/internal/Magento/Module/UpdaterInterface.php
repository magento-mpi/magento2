<?php
/**
 * DB updater interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module;

interface UpdaterInterface
{
    /**
     * Apply database scheme updates whenever needed
     *
     * @return void
     */
    public function updateScheme();

    /**
     * Apply database data updates whenever needed
     *
     * @return void
     */
    public function updateData();
}
