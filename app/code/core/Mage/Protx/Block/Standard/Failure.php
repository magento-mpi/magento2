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
 * @package    Mage_Protx
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Failure Response from Protx
 *
 * @category   Mage
 * @package    Mage_Protx
 * @name       Mage_Protx_Block_Standard_Failure
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Protx_Block_Standard_Failure extends Mage_Core_Block_Template
{
    /**
     *  Return StatusDetail field value from Response
     *
     *  @return	  string
     */
    public function getErrorMessage ()
    {
        $error = Mage::getSingleton('checkout/session')->getErrorMessage();
        Mage::getSingleton('checkout/session')->unsErrorMessage();
        return $error;
    }

    /**
     * Get continue shopping url
     */
    public function getContinueShoppingUrl()
    {
        return Mage::getUrl('checkout/cart');
    }
}