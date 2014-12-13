<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\SampleData;

/**
 * Class Logger
 */
class Logger
{
    /**
     * Logs a message
     *
     * @param string $message
     * @return void
     */
    public function log($message)
    {
        echo $message;
    }
}
