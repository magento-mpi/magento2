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
class Magento_Downloader_Model_Config_Abstract extends Magento_Downloader_Model
{
    /**
    * Retrive file name
    *
    * @return string
    */
    public function getFilename()
    {
        return $this->controller()->filepath('config.ini');
    }

    /**
    * Load file
    *
    * @return Magento_Downloader_Model_Config
    */
    public function load()
    {
        if (!file_exists($this->getFilename())) {
            return $this;
        }
        $rows = file($this->getFilename());
        if (!$rows) {
            return $this;
        }
        foreach ($rows as $row) {
            $arr = explode('=', $row, 2);
            if (count($arr)!==2) {
                continue;
            }
            $key = trim($arr[0]);
            $value = trim($arr[1], " \t\"'\n\r");
            if (!$key || $key[0]=='#' || $key[0]==';') {
                continue;
            }
            $this->set($key, $value);
        }
        return $this;
    }

    /**
    * Save file
    *
    * @return Magento_Downloader_Model_Config
    */
    public function save()
    {
        if ((!is_writable($this->getFilename())&&is_file($this->getFilename()))||(dirname($this->getFilename())!=''&&!is_writable(dirname($this->getFilename())))) {
            if(isset($this->_data['ftp'])&&!empty($this->_data['ftp'])&&strlen($this->get('downloader_path'))>0){
                $confFile=$this->get('downloader_path').DIRECTORY_SEPARATOR.basename($this->getFilename());
                $ftpObj = new Magento_Connect_Ftp();
                $ftpObj->connect($this->_data['ftp']);
                $tempFile = tempnam(sys_get_temp_dir(),'configini');
                $fp = fopen($tempFile, 'w');
                foreach ($this->_data as $k=>$v) {
                    fwrite($fp, $k.'='.$v."\n");
                }
                fclose($fp);
                $ret=$ftpObj->upload($confFile, $tempFile);
                $ftpObj->close();
            }else{
                /* @TODO: show Warning message*/
                $this->controller()->session()
                    ->addMessage('warning', 'Invalid file permissions, could not save configuration.');
                return $this;
            }
            /**/
        }else{
            $fp = fopen($this->getFilename(), 'w');
            foreach ($this->_data as $k=>$v) {
                fwrite($fp, $k.'='.$v."\n");
            }
            fclose($fp);
        }
        return $this;
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
        switch($channel)
        {
            case 'community':
                $channelLabel = 'Magento Community Edition';
                break;
            default:
                $channelLabel = $channel;
                break;
        }
        return $channelLabel;
    }
}
?>
