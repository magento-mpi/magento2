<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Connect\Command;

final class Channels extends \Magento\Framework\Connect\Command
{
    /**
     * List available channels
     *
     * @param string $command
     * @param array $options
     * @param string[] $params
     * @return void
     */
    public function doList($command, $options, $params)
    {

        try {
            $title = "Available channels:";
            $aliasT = "Available aliases:";
            $packager = $this->getPackager();
            $ftp = empty($options['ftp']) ? false : $options['ftp'];
            if ($ftp) {
                list($cache, $config, $ftpObj) = $packager->getRemoteConf($ftp);
                $data = $cache->getData();
                @unlink($config->getFilename());
                @unlink($cache->getFilename());
            } else {
                $cache = $this->getSconfig();
                $config = $this->config();
                $data = $cache->getData();
            }
            $out = array($command => array('data' => $data, 'title' => $title, 'title_aliases' => $aliasT));
            $this->ui()->output($out);
        } catch (\Exception $e) {
            $this->doError($command, $e->getMessage());
        }
    }

    /**
     * Channel-delete callback method
     *
     * @param string $command
     * @param array $options
     * @param string[] $params
     * @return void
     */
    public function doDelete($command, $options, $params)
    {
        $this->cleanupParams($params);
        try {
            if (count($params) != 1) {
                throw new \Exception("Parameters count should be equal to 1");
            }
            $packager = $this->getPackager();

            $ftp = empty($options['ftp']) ? false : $options['ftp'];
            if ($ftp) {
                list($cache, $config, $ftpObj) = $packager->getRemoteConf($ftp);
                $cache->deleteChannel($params[0]);
                $packager->writeToRemoteCache($cache, $ftpObj);
                @unlink($config->getFilename());
            } else {
                $config = $this->config();
                $cache = $this->getSconfig();
                $cache->deleteChannel($params[0]);
            }
            $this->ui()->output("Successfully deleted");
        } catch (\Exception $e) {
            $this->doError($command, $e->getMessage());
        }
    }

    /**
     * Channel-add callback
     *
     * @param string $command
     * @param array $options
     * @param string[] $params
     * @return void
     */
    public function doAdd($command, $options, $params)
    {
        $this->cleanupParams($params);
        try {
            if (count($params) != 1) {
                throw new \Exception("Parameters count should be equal to 1");
            }
            $url = $params[0];
            $rest = $this->rest();
            $rest->setChannel($url);
            $data = $rest->getChannelInfo();
            $data->url = $url;

            $packager = $this->getPackager();
            $ftp = empty($options['ftp']) ? false : $options['ftp'];
            if ($ftp) {
                list($cache, $config, $ftpObj) = $packager->getRemoteConf($ftp);
                $cache->addChannel($data->name, $url);
                $packager->writeToRemoteCache($cache, $ftpObj);
                @unlink($config->getFilename());
            } else {
                $cache = $this->getSconfig();
                $config = $this->config();
                $cache->addChannel($data->name, $url);
            }

            $this->ui()->output("Successfully added: " . $url);
        } catch (\Exception $e) {
            $this->doError($command, $e->getMessage());
        }
    }

    /**
     * Get information about given channel callback
     *
     * @param string $command
     * @param array $options
     * @param array $params
     * @return void
     */
    public function doInfo($command, $options, $params)
    {
    }

    /**
     * Channel-alias
     *
     * @param string $command
     * @param array $options
     * @param string[] $params
     * @return void
     */
    public function doAlias($command, $options, $params)
    {
        $this->cleanupParams($params);
        try {
            if (count($params) != 2) {
                throw new \Exception("Parameters count should be equal to 2");
            }

            $packager = $this->getPackager();
            $chanUrl = $params[0];
            $alias = $params[1];
            $ftp = empty($options['ftp']) ? false : $options['ftp'];
            if ($ftp) {
                list($cache, $config, $ftpObj) = $packager->getRemoteConf($ftp);
                $cache->addChannelAlias($chanUrl, $alias);
                $packager->writeToRemoteCache($cache, $ftpObj);
                @unlink($config->getFilename());
            } else {
                $cache = $this->getSconfig();
                $config = $this->config();
                $cache->addChannelAlias($chanUrl, $alias);
            }
            $this->ui()->output("Successfully added: " . $alias);
        } catch (\Exception $e) {
            $this->doError($command, $e->getMessage());
        }
    }

    /**
     * @param string $command
     * @param array $options
     * @param string[] $params
     * @return void
     */
    public function doLogin($command, $options, $params)
    {
    }

    /**
     * @param string $command
     * @param array $options
     * @param string[] $params
     * @return void
     */
    public function doLogout($command, $options, $params)
    {
    }
}
