<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Convert data incoming from data base storage
 */
class Magento_Cron_Model_Config_Converter_Db implements Magento_Config_ConverterInterface
{
    /**
     * Convert data
     *
     * @param mixed $source
     * @return array
     */
    public function convert($source)
    {
        $jobs = isset($source['crontab']['jobs']) ? $source['crontab']['jobs'] : array();

        if (empty($jobs)) {
            return $jobs;
        }
        return $this->_extractParams($jobs);
    }

    /**
     * Extract and prepare cron job data
     *
     * @param array $jobs
     * @return array
     */
    protected function _extractParams(array $jobs)
    {
        $result = array();
        foreach ($jobs as $jobName => $value) {
            $result[$jobName] = $value;

            if (isset($value['schedule']) && is_array($value['schedule'])) {
                $this->_processConfigParam($value, $jobName, $result);
                $this->_processScheduleParam($value, $jobName, $result);
            }

            $this->_processRunModel($value, $jobName, $result);
        }
        return $result;
    }

    /**
     * Fetch parameter 'config_path' from 'schedule' container
     *
     * @param array  $jobConfig
     * @param string $jobName
     * @param array  $result
     */
    protected function _processConfigParam(array $jobConfig, $jobName, array &$result)
    {
        if (array_key_exists('config_path', $jobConfig['schedule'])) {
            $result[$jobName]['config_path'] = $jobConfig['schedule']['config_path'];
        }
    }
    /**
     * Fetch parameter 'cron_expr' from 'schedule' container, reassign it
     *
     * @param array  $jobConfig
     * @param string $jobName
     * @param array  $result
     */
    protected function _processScheduleParam(array $jobConfig, $jobName, array &$result)
    {
        if (array_key_exists('cron_expr', $jobConfig['schedule'])) {
            $result[$jobName]['schedule'] = $jobConfig['schedule']['cron_expr'];
        }
    }

    /**
     * Fetch parameters from 'run' container and save it by reference
     *
     * @param array  $jobConfig
     * @param string $jobName
     * @param array  $result
     */
    protected function _processRunModel(array $jobConfig, $jobName, array &$result)
    {
        if (isset($jobConfig['run']) && is_array($jobConfig['run']) && array_key_exists('model', $jobConfig['run'])) {
            $callPath = explode('::', $jobConfig['run']['model']);

            if (empty($callPath) || empty($callPath[0]) || empty($callPath[1])) {
                unset($result[$jobName]['run']);
                return;
            }

            $result[$jobName]['instance'] = $callPath[0];
            $result[$jobName]['method'] = $callPath[1];
            unset($result[$jobName]['run']);
        }
    }
}
