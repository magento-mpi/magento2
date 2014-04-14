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
class Enterprise extends \Magento\Downloader\Model\Config\AbstractConfig implements
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
     * Get Auth data from config
     * @param mixed $session Session object
     * @return array auth data
     */
    private function _getAuth($session)
    {
        $auth = $session->get('auth');
        return array_values($auth);
    }

    /**
     * Set data for Settings View
     *
     * @param \Magento\Connect\Config $config
     * @param \Magento\Downloader\View $view
     * @return void
     */
    public function setInstallView($config, $view)
    {
        $root_channel = $this->get('root_channel');
        $view->set('channel_logo', $root_channel);
        $view->set('channel_steps', $view->template($root_channel . '/install_steps.phtml'));
        $view->set('channel_notice', $view->template($root_channel . '/install_notice.phtml'));
        $view->set('channel_protocol_fields', $view->template($root_channel . '/auth.phtml'));
    }

    /**
     * Set data for Settings View
     * @param mixed $session Session object
     * @param \Magento\Downloader\View $view
     * @return void
     */
    public function setSettingsView($session, $view)
    {
        $auth = $this->_getAuth($session);
        if ($auth) {
            $view->set('auth_username', isset($auth[0]) ? $auth[0] : '');
            $view->set('auth_password', isset($auth[1]) ? $auth[1] : '');
        }
        $view->set('channel_protocol_fields', $view->template($this->get('root_channel') . '/auth.phtml'));
    }

    /**
     * Set session data for Settings
     * @param array $post post data
     * @param mixed $session Session object
     * @return void
     */
    public function setSettingsSession($post, $session)
    {
        if (isset($post['auth_username']) && isset($post['auth_password'])) {
            $session->set('auth', array('username' => $post['auth_username'], 'password' => $post['auth_password']));
        } else {
            $session->set('auth', array());
        }
    }

    /**
     * Get root channel URI
     *
     * @return string Root channel URI
     */
    public function getRootChannelUri()
    {
        if (!$this->get('root_channel_uri')) {
            $this->set('root_channel_uri', 'connect20.magentocommerce.com/enterprise');
        }
        return $this->get('root_channel_uri');
    }

    /**
     * Set config data from POST
     *
     * @param \Magento\Connect\Config $config Config object
     * @param array $post post data
     * @return void
     */
    public function setPostData($config, &$post)
    {
        if (!empty($post['auth_username']) and isset($post['auth_password'])) {
            $post['auth'] = $post['auth_username'] . '@' . $post['auth_password'];
        } else {
            $post['auth'] = '';
        }
        if (!is_null($config)) {
            $config->auth = $post['auth'];
            $config->root_channel_uri = $this->getRootChannelUri();
            $config->root_channel = $this->get('root_channel');
        }
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
        $auth = $this->_getAuth($session);
        $options['auth'] = array('username' => $auth[0], 'password' => $auth[1]);
    }

    /**
     * Return channel label for channel name
     *
     * @param string $channel
     * @return string
     */
    public function getChannelLabel($channel)
    {
        $channelLabel = '';
        switch ($channel) {
            case 'community':
                $channelLabel = 'Magento Community Edition';
                break;
            case 'enterprise':
                $channelLabel = 'Magento Enterprise Edition';
                break;
            default:
                $channelLabel = $channel;
                break;
        }
        return $channelLabel;
    }
}
