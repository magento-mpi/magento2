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
 * @package     Mage_Paygate
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Enter description here ...
 *
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug _getResource()
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug getResource()
 * @method Mage_Paygate_Model_Authorizenet_Debug getRequestBody()
 * @method string setRequestBody(string $value)
 * @method Mage_Paygate_Model_Authorizenet_Debug getResponseBody()
 * @method string setResponseBody(string $value)
 * @method Mage_Paygate_Model_Authorizenet_Debug getRequestSerialized()
 * @method string setRequestSerialized(string $value)
 * @method Mage_Paygate_Model_Authorizenet_Debug getResultSerialized()
 * @method string setResultSerialized(string $value)
 * @method Mage_Paygate_Model_Authorizenet_Debug getRequestDump()
 * @method string setRequestDump(string $value)
 * @method Mage_Paygate_Model_Authorizenet_Debug getResultDump()
 * @method string setResultDump(string $value)
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paygate_Model_Authorizenet_Debug extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('paygate/authorizenet_debug');
    }
}
