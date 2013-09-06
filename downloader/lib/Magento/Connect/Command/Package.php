<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

final class \Magento\Connect\Command\Package
extends \Magento\Connect\Command
{
    /**
     * Dependencies list
     * @var array
     */
    private $_depsList = array();

    /**
     * Releases list
     * @var array
     */
    private $_releasesList = array();

    /**
     * Package command callback
     * @param string $command
     * @param array $options
     * @param array $params
     * @return void
     */
    public function doPackage($command, $options, $params)
    {
        $this->cleanupParams($params);

        if(count($params) < 1) {
            return $this->doError($command, "Parameters count should be >= 1");
        }

        $file = strtolower($params[0]);
        $file = realpath($file);

        if(!file_exists($file)) {
            return $this->doError($command, "File {$params[0]} doesn't exist");
        }

        try {
            $packager = new \Magento\Connect\Package($file);
            $res = $packager->validate();
            if(!$res) {
                $this->doError($command, implode("\n", $packager->getErrors()));
                return;
            }
            $packager->save(dirname($file));
            $this->ui()->output('Done building package');
        } catch (Exception $e) {
            $this->doError( $command, $e->getMessage() );
        }
    }

    /**
     * Display/get installation information for package
     * @param string $command
     * @param array $options
     * @param array $params
     * @return void/array
     */
    public function doPackagePrepare($command, $options, $params)
    {
        $this->cleanupParams($params);
        $channelAuth = array();
        if (isset($options['auth'])) {
            $channelAuth = $options['auth'];
            $options['auth'] = null;
        }
        try {

            if(count($params) < 2) {
                return $this->doError($command, "Argument count should be >= 2");
            }

            $channel = $params[0];
            $package = $params[1];

            $argVersionMin = isset($params[3]) ? $params[3] : false;
            $argVersionMax = isset($params[2]) ? $params[2] : false;

            $ftp = empty($options['ftp']) ? false : $options['ftp'];
            $packager = $this->getPackager();
            if ($ftp) {
                list($cache, $config, $ftpObj) = $packager->getRemoteConf($ftp);
            } else {
                $cache = $this->getSconfig();
                $config = $this->config();
            }

            $rest = new \Magento\Connect\Rest($config->protocol);
            if(!empty($channelAuth)){
                $rest->getLoader()->setCredentials($channelAuth['username'], $channelAuth['password']);
            }

            $cache->checkChannel($channel, $config, $rest);

            $data = $packager->getDependenciesList($channel, $package, $cache, $config, 
                    $argVersionMax, $argVersionMin, true, false, $rest
            );
            
            $result = array();
            foreach ($data['result'] as $_package) {
                $_result['channel'] = $_package['channel'];
                $_result['name'] = $_package['name'];
                $_result['version'] = $_package['downloaded_version'];
                $_result['stability'] = $_package['stability'];
                $_result['install_state'] = $_package['install_state'];
                $_result['message'] = $_package['message'];
                $result[] = $_result;
            }
            if (!count($data['result']) && isset($data['failed']) && !empty($data['failed'])) {
                foreach ($data['failed'] as $_package) {
                    $reason = $_package['channel'] . '/' . $_package['name'] . ': ' . $_package['reason'];
                    $this->doError($command, $reason);
                }
            }

            $this->ui()->output(array($command=> array('data'=>$result, 'title'=>"Package installation information for {$params[1]}: ")));

        } catch (Exception $e) {
            $this->doError($command, $e->getMessage());
        }
    }

    /**
     * Display/get dependencies
     * @param string $command
     * @param array $options
     * @param array $params
     * @return void/array
     */
    public function doPackageDependencies($command, $options, $params)
    {
        $this->cleanupParams($params);
        try {
            if(count($params) < 2) {
                return $this->doError($command, "Argument count should be >= 2");
            }

            $channel = $params[0];
            $package = $params[1];

            $argVersionMin = isset($params[3]) ? $params[3] : false;
            $argVersionMax = isset($params[2]) ? $params[2] : false;

            $ftp = empty($options['ftp']) ? false : $options['ftp'];
            $packager = $this->getPackager();
            if($ftp) {
                list($cache, $config, $ftpObj) = $packager->getRemoteConf($ftp);
            } else {
                $cache = $this->getSconfig();
                $config = $this->config();
            }
            $data = $packager->getDependenciesList($channel, $package, $cache, $config, $argVersionMax, $argVersionMin);
            $this->ui()->output(array($command=> array('data'=>$data['deps'], 'title'=>"Package deps for {$params[1]}: ")));

        } catch (Exception $e) {
            $this->doError($command, $e->getMessage());
        }
    }

    public function doConvert($command, $options, $params)
    {
        $this->cleanupParams($params);
        try {
            if(count($params) < 1) {
                throw new Exception("Arguments should be: source.tgz [target.tgz]");
            }
            $sourceFile = $params[0];
            $converter = new \Magento\Connect\Converter();
            $targetFile = isset($params[1]) ? $params[1] : false;
            $result = $converter->convertPearToMage($sourceFile, $targetFile);
            $this->ui()->output("Saved to: ".$result);
        } catch (Exception $e) {
            $this->doError($command, $e->getMessage());
        }

    }

}
