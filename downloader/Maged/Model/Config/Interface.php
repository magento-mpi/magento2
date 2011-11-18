<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
* Class config
*
* @category   Mage
* @package    Mage_Connect
* @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
interface Maged_Model_Config_Interface
{

    /**
     * Set data for Settings View
     *
     * @param Mage_Connect_Config $config
     * @param Maged_View $view
     * @return null
     */
    public function setInstallView($config, $view);

    /**
     * Set data for Settings View
     *
     * @param mixed $session Session object
     * @param Maged_View $view
     * @return null
     */
    public function setSettingsView($session, $view);

    /**
     * Set session data for Settings
     *
     * @param array $post post data
     * @param mixed $session Session object
     * @return null
     */
    public function setSettingsSession($post, $session);

    /**
     * Set config data from POST
     *
     * @param Mage_Connect_Config $config Config object
     * @param array $post post data
     * @return boolean
     */
    public function setPostData($config, &$post);

    /**
     * Get root channel URI
     *
     * @return string Root channel URI
     */
    public function getRootChannelUri();

    /**
     * Set additional command options
     *
     * @param mixed $session Session object
     * @param array $options
     * @return null
     */
    public function setCommandOptions($session, &$options);
}
?>
