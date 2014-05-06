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
     * @param int $length
     * @param null|string $chars
     * @return string
     */
    public function getRandomString($length, $chars = null)
    {
        $str = '';
        if (is_null($chars)) {
            $chars = self::CHARS_LOWERS . self::CHARS_UPPERS . self::CHARS_DIGITS;
        }
        $bytes = '';
        $fp = null;
        if (function_exists('openssl_random_pseudo_bytes')) {
            // use openssl lib if it is installed
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
            $hex = bin2hex($bytes); // hex() doubles the length of the string
            $str = substr($hex, 0, $length); // we truncate at most 1 char if length parameter is an odd number
        } elseif ($fp = @fopen('/dev/urandom', 'rb')) {
            // attempt to use /dev/urandom if it exists but openssl isn't available
            $bytes .= @fread($fp, $length);
            fclose($fp);
            $hex = bin2hex($bytes); // hex() doubles the length of the string
            $str = substr($hex, 0, $length); // we truncate at most 1 char if length parameter is an odd number
        } else {
            // fallback to mt_rand() if all else fails
            for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $length; $i++) {
                $str .= $chars[mt_rand(0, $lc)];
            }
            // log error here to warn merchant of insecure randomness
        }
        return $str;
    }

    /**
     * Generate a hash from unique ID
     *
     * @param string $prefix
     * @return string
     */
    public function getUniqueHash($prefix = '')
    {
        return $prefix . md5(uniqid(microtime() . mt_rand(), true));
    }
}
