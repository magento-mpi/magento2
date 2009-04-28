<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Logging_Model_Event extends Mage_Core_Model_Abstract
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_init('enterprise_logging/event');
    }

    /**
     * Filter user_id. Set username instead of user_id
     *
     * @param int $id
     * @return Enterprise_Logging_Model_Event
     */
    public function setUserId($id) 
    {
        if (!$this->getUser() && $id) {
            $user = Mage::getModel('admin/user')->load($id);
            $name = $user->getUsername();
            return $this->setUser($name);
        }
        return $this;
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
        if (isset($info['event_status']) && $info['event_status'] != $this->getSuccess()) {
            $this->setSuccess($info['event_status']);
        }

        $success = $this->getSuccess() ? 'success' : 'fail';
        $this->setStatus($success);
        return $this->setData('info', $info['event_message']);
    }
}
