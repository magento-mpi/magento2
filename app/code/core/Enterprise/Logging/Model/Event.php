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
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Logging_Model_Event extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('logging/event');
    }

    public function isActive($code)
    {
        /**
         * Note that /default/logging/enabled/products - is an indicator if the products should be logged
         * but /enterprise/logging/event/products - is a node where event info stored.
         */
        $node = Mage::getConfig()->getNode('default/logging/enabled/'.$code);
        return ( (string)$node == '1' ? true : false);
    }

    public function setInfo($info) {
        $code = $this->getEventCode();
        $action = $this->getAction();
        $success = $this->getSuccess() ? 'success' : 'fail';

        $node = Mage::getConfig()->getNode('enterprise/logging/events/'.$code.'/actions/'.$action.'/'.$success);
        $string = (string)$node;
        if(is_array($info)) {
            $args = array_unshift($info, $string);
            try {
                $string = call_user_func_array('sprintf', $info);
            } catch(Exception $e) {
                Mage::throwException("Wrong parameters passed to event info. ".$string.";");
            }
        }
        //parent::setInfo($string);
    }

    public function setIp($ip) {
        //parent::setIp(ip2long($ip));
    }
}