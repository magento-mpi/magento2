<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloader\Model\Config;

/**
 * Class config
 *
 * @category   Magento
 * @package    Magento_Connect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Community extends \Magento\Downloader\Model\Config\AbstractConfig implements
    \Magento\Downloader\Model\Config\ConfigInterface
{
    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->load();
    }

    /**
     * Set data for Settings View
     *
     * @param \Magento\Framework\Connect\Config $config
     * @param \Magento\Downloader\View $view
     * @return void
     */
    public function setInstallView($config, $view)
    {
        $view->set('channel_logo', 'logo');
    }

    /**
     * Set data for Settings View
     * @param \Magento\Framework\Connect\Config $config
     * @param \Magento\Downloader\View $view
     * @return void
     */
    public function setSettingsView($config, $view)
    {
    }

    /**
     * Set session data for Settings
     * @param array $post post data
     * @param mixed $session Session object
     * @return void
     */
    public function setSettingsSession($post, $session)
    {
    }

    /**
     * Get root channel URI
     *
     * @return string Root channel URI
     */
    public function getRootChannelUri()
    {
        if (!$this->get('root_channel_uri')) {
            $this->set('root_channel_uri', 'connect20.magentocommerce.com/community');
        }
        return $this->get('root_channel_uri');
    }

    /**
     * Set config data from POST
     *
     * @param \Magento\Framework\Connect\Config $config Config object
     * @param array $post post data
     * @return void
     */
    public function setPostData($config, &$post)
    {
    }

    /**
     * Set additional command options
     *
     * @param mixed $session Session object
     * @param array $options
     * @return void
     */
    public function setCommandOptions($session, &$options)
    {
    }
}
