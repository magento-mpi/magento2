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
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkout string helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Helper_String extends Mage_Core_Helper_String
{
    /**
     * Truncate item option value, depending of type
     * Output will be escaped
     *
     * $wasTruncated will be true, if item was truncated
     *
     * @param string $value
     * @param string $type
     * @param int $maxLength
     * @param string $etc
     * @param bool &$wasTruncated
     * @return string
     */
    public function truncateOptionValueByType($value, $type, $maxLength = 100, $etc = '...', &$wasTruncated = false)
    {
        if (in_array($type, array('area', 'field'))) {
            $value = $this->truncate($value, $maxLength, $etc, $wasTruncated);
        }
        return $this->escapeOptionValueByType($value, $type);
    }

    /**
     * Escape a value. It will add nl2br, if type is 'area'
     *
     * @param string $value
     * @param string $type
     * @return string
     */
    public function escapeOptionValueByType($value, $type)
    {
        $value = $this->htmlEscape($value);
        if ('area' == $type) {
            $value = nl2br($value);
        }
        return $value;
    }
}