<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Math;

/**
 * Random data generator
 */
class Random
{
    /**#@+
     * Frequently used character classes
     */
    const CHARS_LOWERS = 'abcdefghijklmnopqrstuvwxyz';

    const CHARS_UPPERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    const CHARS_DIGITS = '0123456789';

    /**#@-*/

    /**
     * Get random string
     *
     * @param int         $length
     * @param null|string $chars
     * @return string
     */
    public function getRandomString($length, $chars = null)
    {
        $str = '';
        if (null === $chars) {
            $chars = self::CHARS_LOWERS . self::CHARS_UPPERS . self::CHARS_DIGITS;
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            // use openssl lib if it is installed
            for ($i = 0, $lc = strlen($chars) - 1; $i < $length; $i++) {
                $bytes = openssl_random_pseudo_bytes(PHP_INT_SIZE);
                $hex = bin2hex($bytes); // hex() doubles the length of the string
                $rand = abs(hexdec($hex) % $lc); // random integer from 0 to $lc
                $str .= $chars[$rand]; // random character in $chars
            }
        } elseif ($fp = @fopen('/dev/urandom', 'rb')) {
            // attempt to use /dev/urandom if it exists but openssl isn't available
            for ($i = 0, $lc = strlen($chars) - 1; $i < $length; $i++) {
                $bytes = @fread($fp, PHP_INT_SIZE);
                $hex = bin2hex($bytes); // hex() doubles the length of the string
                $rand = abs(hexdec($hex) % $lc); // random integer from 0 to $lc
                $str .= $chars[$rand]; // random character in $chars
            }
            fclose($fp);
        } else {
            // fallback to mt_rand() if all else fails
            mt_srand(10000000 * (double)microtime());
            for ($i = 0, $lc = strlen($chars) - 1; $i < $length; $i++) {
                $rand = mt_rand(0, $lc); // random integer from 0 to $lc
                $str .= $chars[$rand]; // random character in $chars
            }
        }

        return $str;
    }
}
