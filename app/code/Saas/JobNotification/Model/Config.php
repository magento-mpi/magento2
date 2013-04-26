<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_JobNotification_Model_Config
{
    /**
     * Jobs configuration
     *
     * @var array
     */
    protected $_configuration = array();

    /**
     * @param Mage_Core_Model_ConfigInterface $config
     */
    public function __construct(Mage_Core_Model_ConfigInterface $config)
    {
        foreach($config->getNode('global/tasks')->asArray() as $taskCode => $taskConfig) {
            $notificationConfig = isset($taskConfig['notification']) ? $taskConfig['notification'] : array();
            $this->_configuration[$taskCode] = array(
                'enabled' => isset($notificationConfig['enabled']) && 'true' == $notificationConfig['enabled'],
                'title'   => isset($notificationConfig['title']) ? $notificationConfig['title'] : '',
            );
         }
    }

    /**
     * Get config value
     *
     * @param string $jobName
     * @param string $key
     * @param null $default
     * @return mixed
     */
    protected function _getValue($jobName, $key, $default = null)
    {
        return array_key_exists($jobName, $this->_configuration) ? $this->_configuration[$jobName][$key] : $default;
    }

    /**
     * Check whether notification allowed
     *
     * @param string $jobName
     * @return bool
     */
    public function isNotificationAllowed($jobName)
    {
        return $this->_getValue($jobName, 'enabled', false);
    }

    /**
     * Get job title
     *
     * @param string $jobName
     * @return string
     */
    public function getJobTitle($jobName)
    {
        return $this->_getValue($jobName, 'title', '');
    }
}