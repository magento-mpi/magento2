<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Helper_String extends Mage_Core_Helper_Abstract
{
    const ICONV_CHARSET = 'UTF-8';

    /**
     * Truncate a string to a certain length if necessary,
     * optionally splitting in the middle of a word,
     * and appending the $etc string or inserting $etc into the middle.
     *
     * @param string $string
     * @param int $length
     * @param string $etc
     * @param bool &$wasTruncated
     * @param bool $breakWords
     * @param bool $middle
     * @return string
     */
    public function truncate($string, $length = 80, $etc = '...', &$wasTruncated = false, $breakWords = false, $middle = false)
    {
        $wasTruncated = false;
        if (0 == $length) {
            return '';
        }

        if (iconv_strlen($string, self::ICONV_CHARSET) > $length) {
            $length -= iconv_strlen($etc, self::ICONV_CHARSET);
            if (!$breakWords && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', iconv_substr($string, 0, $length + 1, self::ICONV_CHARSET));
            }
            $wasTruncated = true;
            if (!$middle) {
                return iconv_substr($string, 0, $length, self::ICONV_CHARSET) . $etc;
            }
            else {
                return iconv_substr($string, 0, $length/2, self::ICONV_CHARSET) . $etc . iconv_substr($string, -$length/2, null, self::ICONV_CHARSET);
            }
        }

        return $string;
    }
}