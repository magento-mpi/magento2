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
 * @category   Mage
 * @package    Mage_Tests
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice API credentials supplier, that will delete its resources before vanishing
 */
class WebService_Connector_Credentials
{
    private $_role;
    private $_user;
    private $_url;
    private $_id;

    /**
     * Claim and incapsulate given resources
     *
     * $id is used both for username and password
     *
     * @param Varien_Object $role
     * @param Varien_Object $user
     * @param string $id
     * @param string $url
     */
    function __construct(Varien_Object $role, Varien_Object $user, $id, $url)
    {
        $this->_role = $role;
        $this->_user = $user;
        $this->_url  = $url;
        $this->_id   = $id;
    }

    /**
     * Dispose of resources
     *
     */
    function __destruct()
    {
        $this->_user->delete();
        $this->_role->delete();
    }

    /**
     * Get username for Webservice API
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->_id;
    }

    /**
     * Get password for Webservice API
     *
     * @return string
     */
    public function getApiConfirmation()
    {
        return $this->_id;
    }

    /**
     * Get Magento installation base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_url;
    }
}
