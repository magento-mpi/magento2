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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth Helper
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**#@+
     * Endpoint types
     */
    const ENDPOINT_AUTHORIZE = 'authorize';
    const ENDPOINT_INITIATE  = 'initiate';
    const ENDPOINT_TOKEN     = 'token';
    /**#@-*/

    /**#@+
     * Display types
     */
    const DISPLAY_TYPE_CUSTOMER = 'customer';
    const DISPLAY_TYPE_ADMIN    = 'admin';
    /**#@-*/

    /**
     * Generate random string for token or secret
     *
     * @param int $length String length
     * @return string
     */
    public function generateToken($length)
    {
        /** @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('core');

        return $helper->getRandomString(
            $length, Mage_Core_Helper_Data::CHARS_DIGITS . Mage_Core_Helper_Data::CHARS_LOWERS
        );
    }

    /**
     * Retrieve URL of specified endpoint.
     *
     * @param string $type Endpoint type (one of ENDPOINT_ constants)
     * @return string
     */
    public function getProtocolEndpointUrl($type)
    {
        if (self::ENDPOINT_INITIATE != $type && self::ENDPOINT_AUTHORIZE != $type && self::ENDPOINT_TOKEN != $type) {
            Mage::throwException('Invalid endpoint type passed');
        }
        return rtrim(Mage::getUrl('oauth/' . $type), '/');
    }

    /**
     * Check display type validation
     *
     * @param  string $displayType
     * @return bool
     */
    public function isValidDisplayType($displayType)
    {
        return self::DISPLAY_TYPE_ADMIN === $displayType || self::DISPLAY_TYPE_CUSTOMER === $displayType;
    }
}
