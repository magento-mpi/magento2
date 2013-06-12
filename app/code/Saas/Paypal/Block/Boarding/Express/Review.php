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
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Paypal Express review checkout block for permissions
 */
class Saas_Paypal_Block_Boarding_Express_Review extends Mage_Paypal_Block_Express_Review
{
    /**
     * Replace 'Place Order' link
     *
     * @return Saas_Paypal_Block_Boarding_Express_Review
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $this->setPlaceOrderUrl($this->getUrl("{$this->_paypalActionPrefix}/boarding_express/placeOrder"));
        return $this;
    }

}
