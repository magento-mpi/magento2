<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
