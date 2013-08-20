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
class Magento_Downloader_Model_Config_Community extends Magento_Downloader_Model_Config_Abstract implements Magento_Downloader_Model_Config_Interface
{

    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->load();
    }

    /**
     * Set data for Settings View
     *
     * @param Magento_Connect_Config $config
     * @param Magento_Downloader_View $view
     * @return null
     */
    public function setInstallView($config, $view)
    {
        $view->set('channel_logo', 'logo');
    }

    /**
     * Set data for Settings View
     * @param Magento_Connect_Config $config
     * @param Magento_Downloader_View $view
     * @return null
     */
    public function setSettingsView($config, $view)
    {
    }

    /**
     * Set session data for Settings
     * @param array $post post data
     * @param mixed $session Session object
     * @return null
     */
    public function setSettingsSession($post, $session)
    {
    }

    /**
     * Get root channel URI
     *
     * @return string Root channel URI
     */
    public function getRootChannelUri(){
        if (!$this->get('root_channel_uri')) {
            $this->set('root_channel_uri', 'connect20.magentocommerce.com/community');
        }
        return $this->get('root_channel_uri');
    }

    /**
     * Set config data from POST
     *
     * @param Magento_Connect_Config $config Config object
     * @param array $post post data
     * @return null
     */
    public function setPostData($config, &$post)
    {
    }

    /**
     * Set additional command options
     *
     * @param mixed $session Session object
     * @param array $options
     * @return null
     */
    public function setCommandOptions($session, &$options)
    {
    }
}
?>
