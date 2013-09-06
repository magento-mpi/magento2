<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class config
 *
 * @category   Magento
 * @package    Magento_Connect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Maged_Model_Config_Interface
{

    /**
     * Set data for Settings View
     *
     * @param Magento_Connect_Config $config
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
     * @param Magento_Connect_Config $config Config object
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
