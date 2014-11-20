<?php
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
