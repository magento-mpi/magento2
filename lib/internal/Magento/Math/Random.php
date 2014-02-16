<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Math;

/**
 * Random data generator
 */
class Random
{
    const CHARS_LOWERS                          = 'abcdefghijklmnopqrstuvwxyz';
    const CHARS_UPPERS                          = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const CHARS_DIGITS                          = '0123456789';
    const CHARS_SPECIALS                        = '!$*+-.=?@^_|~';
    const CHARS_PASSWORD_LOWERS                 = 'abcdefghjkmnpqrstuvwxyz';
    const CHARS_PASSWORD_UPPERS                 = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    const CHARS_PASSWORD_DIGITS                 = '23456789';
    const CHARS_PASSWORD_SPECIALS               = '!$*-.=?@_';

    /**
     * Permission level to deny access
     */
    const RULE_PERM_DENY = 0;

    /**
     * Permission level to inherit access from parent role
     */
    const RULE_PERM_INHERIT = 1;

    /**
     * Permission level to allow access
     */
    const RULE_PERM_ALLOW = 2;

    /**
     * Get random string
     *
     * @param int $length
     * @param null|string $chars
     * @return string
     */
    public function getRandomString($length, $chars = null)
    {
        if (is_null($chars)) {
            $chars = self::CHARS_LOWERS . self::CHARS_UPPERS . self::CHARS_DIGITS;
        }
        mt_srand(10000000 * (double)microtime());
        for ($i = 0, $string = '', $lc = strlen($chars)-1; $i < $length; $i++) {
            $string .= $chars[mt_rand(0, $lc)];
        }
        return $string;
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
