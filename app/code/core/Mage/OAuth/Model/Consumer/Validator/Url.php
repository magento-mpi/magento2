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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Validate consumer URL
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Model_Consumer_Validator_Url extends Zend_Validate_Abstract
{
    /**#@+
     * Error keys
     */
    const NO_OAUTH_PREFIX = 'noOAuthPrefix';
    /**#@-*/

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NO_OAUTH_PREFIX   => "Parameters in URL '%value%' must not contain 'oauth_' prefix. See RFC-5849.",
    );

    /**
     * Validate value
     *
     * @param string|array $value
     * @return bool
     */
    public function isValid($value)
    {
        return !preg_match('/[&?]oauth_/i', $value);
    }
}
