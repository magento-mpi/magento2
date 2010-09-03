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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Helper_Payment extends Mage_Core_Helper_Abstract
{
    /**
     * Retrieve all payment methods list as an array
     * Output assoc array as <code> => <title>
     *
     * @return array
     */
    public function getPaymentMethodCodeList()
    {
        $methods = array();
        $methodList = Mage::getStoreConfig(Mage_Payment_Helper_Data::XML_PATH_PAYMENT_METHODS, null);
        foreach ($methodList as $code => $data) {
            $methods[] = $code;
        }
        asort($methods);
        return $methods;
    }
}
