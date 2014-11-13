<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData;

interface SetupInterface
{
    /**
     * Runs sample data setup process for some module
     *
     * @return void
     */
    public function run();
}
