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
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth authorization block
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 * @method string getToken()
 * @method Mage_OAuth_Block_Authorize setToken() setToken(string $token)
 * @method boolean getIsException()
 * @method Mage_OAuth_Block_Authorize setIsException() setIsException(boolean $flag)
 * @method boolean getIsPopUp()
 * @method Mage_OAuth_Block_Authorize setIsPopUp() setIsPopUp(boolean $flag)
 */
class Mage_OAuth_Block_Authorize_Button extends Mage_Core_Block_Template
{
    /**
     * Retrieve confirm authorization url
     *
     * @return string
     */
    public function getConfirmUrl()
    {
        return $this->getUrl('oauth/authorize/confirm');
    }

    /**
     * Retrieve reject authorization url
     *
     * @return string
     */
    public function getRejectUrl()
    {
        return $this->getUrl('oauth/authorize/reject');
    }
}
