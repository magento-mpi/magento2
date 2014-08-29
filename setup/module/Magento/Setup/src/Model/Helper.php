<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Zend\Console\Request as ConsoleRequest;

class Helper
{
    /**
     * Check the validity of a request if it is from the console or not
     *
     * @param \Zend\Console\Request $request
     * @throws \RuntimeException
     */
    public static function checkRequest($request)
    {
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }
    }

    /**
     * Convert an array to string
     *
     * @param array $input
     * @return string
     */
    public static function arrayToString($input)
    {
        $result = '';
        foreach ($input as $key => $value) {
            $result .= "$key => $value\n";
        }

        return $result;
    }

    /**
     * Check existence of a directory
     *
     * @param $string $destinationDir
     * @throws \Exception
     */
    public static function checkAndCreateDirectory($destinationDir)
    {
        try {
            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0777, true);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
