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
 * Configuration command callbacks
 *
 * @throws \Exception
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Framework\Connect\Command;

class Config extends \Magento\Framework\Connect\Command
{
    /**
     * Parameters constants
     */
    const PARAM_KEY = 0;

    const PARAM_VAL = 1;

    /**
     * Show config variable
     * @param string $command
     * @param array $options
     * @param array $params
     * @return void
     */
    public function doConfigShow($command, $options, $params)
    {
        $this->cleanupParams($params);

        try {
            $values = array();

            $packager = $this->getPackager();
            $ftp = empty($options['ftp']) ? false : $options['ftp'];
            if ($ftp) {
                list($config, $ftpObj) = $packager->getRemoteConfig($ftp);
            } else {
                $config = $this->config();
            }
            foreach ($config as $k => $v) {
                $values[$k] = $v;
            }
            if ($ftp) {
                @unlink($config->getFilename());
            }
            $data = array($command => array('data' => $values));
            $this->ui()->output($data);
        } catch (\Exception $e) {
            if ($ftp) {
                @unlink($config->getFilename());
            }
            return $this->doError($command, $e->getMessage());
        }
    }

    /**
     * Set config variable
     * @param string $command
     * @param array $options
     * @param array $params
     * @return void
     */
    public function doConfigSet($command, $options, $params)
    {
        $this->cleanupParams($params);

        try {
            if (count($params) < 2) {
                throw new \Exception("Parameters count should be >= 2");
            }
            $key = strtolower($params[self::PARAM_KEY]);
            $val = strval($params[self::PARAM_VAL]);
            $packager = $this->getPackager();

            $ftp = empty($options['ftp']) ? false : $options['ftp'];
            if (!$ftp) {
                $config = $this->config();
                $ftp = $config->remote_config;
            }
            if ($ftp) {
                list($cache, $config, $ftpObj) = $packager->getRemoteConf($ftp);
            }

            if (!$config->hasKey($key)) {
                throw new \Exception("No such config variable: {$key}!");
            }
            if (!$config->validate($key, $val)) {
                $possible = $this->config()->possible($key);
                $type = $this->config()->type($key);
                $errString = "Invalid value specified for {$key}!";
                throw new \Exception($errString);
            }
            if ($ftp) {
                $packager->writeToRemoteConfig($config, $ftpObj);
            }
            $this->config()->{$key} = $val;
            $this->ui()->output('Success');
        } catch (\Exception $e) {
            if ($ftp) {
                @unlink($config->getFilename());
            }
            return $this->doError($command, $e->getMessage());
        }
    }

    /**
     * Get config var
     * @param string $command
     * @param array $options
     * @param array $params
     * @return void
     */
    public function doConfigGet($command, $options, $params)
    {
        $this->cleanupParams($params);

        try {
            if (count($params) < 1) {
                throw new \Exception("Parameters count should be >= 1");
            }
            $packager = $this->getPackager();
            $ftp = empty($options['ftp']) ? false : $options['ftp'];
            if ($ftp) {
                list($config, $ftpObj) = $packager->getRemoteConfig($ftp);
            } else {
                $config = $this->config();
            }
            $key = strtolower($params[self::PARAM_KEY]);
            if (!$config->hasKey($key)) {
                throw new \Exception("No such config variable '{$key}'!");
            }
            if ($ftp) {
                @unlink($config->getFilename());
            }
            $this->ui()->output($config->{$key});
        } catch (\Exception $e) {
            if ($ftp) {
                @unlink($config->getFilename());
            }
            return $this->doError($command, $e->getMessage());
        }
    }

    /**
     * Config help
     * @param string $command
     * @param array $options
     * @param array $params
     * @return void
     */
    public function doConfigHelp($command, $options, $params)
    {
        try {
            $this->cleanupParams($params);
            if (count($params) < 1) {
                throw new \Exception("Parameters count should be >= 1");
            }
            $packager = $this->getPackager();
            $ftp = empty($options['ftp']) ? false : $options['ftp'];
            if ($ftp) {
                list($config, $ftpObj) = $packager->getRemoteConfig($ftp);
            } else {
                $config = $this->config();
            }

            $key = strtolower($params[self::PARAM_KEY]);
            if (!$this->config()->hasKey($key)) {
                throw new \Exception("No such config variable '{$key}'!");
            }

            $possible = $config->possible($key);
            $type = $config->type($key);
            $doc = $config->doc($key);
            if ($ftp) {
                @unlink($config->getFilename());
            }
            $data = array();
            $data[$command]['data'] = array(
                'name' => array('Variable name', $key),
                'type' => array('Value type', $type),
                'possible' => array('Possible values', $possible),
                'doc' => $doc
            );
            $this->ui()->output($data);
        } catch (\Exception $e) {
            if ($ftp) {
                @unlink($config->getFilename());
            }
            return $this->doError($command, $e->getMessage());
        }
    }
}
