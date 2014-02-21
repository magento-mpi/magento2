<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Install\App;

class Output
{
    /**
     * Export variable
     *
     * @param mixed $var
     * @return void
     */
    public function export($var)
    {
        var_export($var);
    }

    /**
     * Display message
     *
     * @param string $message
     * @return void
     */
    public function success($message)
    {
        echo $message;
    }

    /**
     * Display error
     *
     * @param string $message
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function error($message)
    {
        echo $message;
        exit(1);
    }
}
