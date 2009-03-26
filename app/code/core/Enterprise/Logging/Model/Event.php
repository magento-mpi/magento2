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
    /**
     * configuration
     */
    private $_config;
    private $_action;
    private $_entity = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_init('enterprise_logging/event');
    }

    /**
     * Filter for active flag
     */
    public function isActive($code)
    {
        /**
         * Note that /default/logging/enabled/products - is an indicator if the products should be logged
         * but /enterprise/logging/event/products - is a node where event info stored.
         */
        $node = Mage::getConfig()->getNode('default/admin/logsenabled/' . $code);
        return ( (string)$node == '1' ? true : false);
    }

    /**
     * Filter user_id. Set username instead of user_id
     *
     * @param int $id
     * @return Enterprise_Logging_Model_Event
     */
    public function setUserId($id) {
        $user = Mage::getModel('admin/user')->load($id);
        $name = $user->getUsername();
        return $this->setUser($name);
    }


    /**
     * Filter for info
     *
     * Takes an array of paramaters required to build info message. Message is stored in config, in
     * path like: enterprise/logging/events/products/actions/success, in sprintf format.
     * Assumed, that parameters in info, follows in order they are required in pattern string
     *
     * @param array $info
     * @return Enterprise_Logging_Model_Event
     */
    public function setInfo($info)
    {
        $code = $info['event_code'];
        $this->setEventCode($code);
        $action = $info['event_action'];
        $this->setAction($action);

        $success = $this->getSuccess() ? 'success' : 'fail';
        $this->setStatus($success);

        return $this->setData('info', $info['event_message']);
    }
}
