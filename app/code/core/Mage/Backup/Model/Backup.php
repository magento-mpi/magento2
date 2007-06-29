<?
/**
 * Backup file item model
 *
 * @package     Mage
 * @subpackage  Backup
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */ 
class Mage_Backup_Model_Backup extends Varien_Object
{
    /* backup types */
    const BACKUP_DB     = "db";
    const BACKUP_VIEW   = "view";
    const BACKUP_MEDIA  = "media";
    
    /* internal constants */
    const BACKUP_EXTENSION  = "backup";
    const COMPRESS_RATE     = 7;

    /**
     * Load backup file info
     *
     * @param string fileName
     * @param string filePath
     * @return Mage_Backup_Model_Backup
     */
    public function load($fileName, $filePath)
    {
        list ($time, $type) = explode("_", substr($fileName, 0, strrpos($fileName, ".")));
        $this->addData(array('time' => (int)$time,
                             'type' => $type,
                             'path' => $filePath,
                             'time_formated' => date('m/d/Y H:i:s', (int)$time)));
        return $this;
    }
    
    /**
     * Checks backup file exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return file_exists($this->getFilePath());
    }
    
    /**
     * Return full file path of backup file
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->getPath() . DS . $this->getTime() . "_" . $this->getType() 
               . "." . self::BACKUP_EXTENSION;
    }
    
    /**
     * Set the backup file content
     *
     * @param string $content
     * @return Mage_Backup_Model_Backup
     */
    public function setFile(&$content)
    {
        if (!$this->hasData('time') || !$this->hasData('type') || !$this->hasData('path')) {
            throw Mage::exception('Mage_Backup','Wrong order of creation new backup');
        }
        if (!is_string($content)) {
            return $this;
        }
        if (!is_dir($this->getPath())) {
            mkdir($this->getPath(),0755);
            chmod($this->getPath(),0755);
        }
        
        $compress = 0;
        if (extension_loaded("zlib")) {
            $compress = 1;
        }
        
        $fResource = @fopen($this->getFilePath(), "wb");
        if (!$fResource) {
            throw Mage::exception('Mage_Backup',"Couldn't write backup file");
        }
        
        $rawContent = '';
        if ( $compress ) {
            $rawContent = gzcompress( $content, self::COMPRESS_RATE );
        } else {
            $rawContent = $content;
        }
        
        
        fwrite($fResource, pack("ll", $compress, strlen($rawContent)));
        fwrite($fResource, pack("a*", $rawContent));
        fclose($fResource);
        
        return $this;
    }
    
    /**
     * Return content of backup file
     *
     * @return string
     */
    public function &getFile() {
        
        if (!$this->exists()) {
            throw Mage::exception('Mage_Backup',"Backup file doesn't exists");
        }
        
        $fResource = @fopen($this->getFilePath(), "rb");
        if (!$fResource) {
            throw Mage::exception('Mage_Backup',"Couldn't read backup file");
        }
        
        $content = '';
        $compressed = 0;
        
        $info = unpack("lcompress/llength", fread($fResource, 8));
        if ($info['compress']) { // If file compressed by zlib
            $compressed = 1;
        }
        if ($compressed && !extension_loaded("zlib")) {
            fclose($fResource);
            throw Mage::exception('Mage_Backup','File compressed with Zlib, but this extension not installed on server');
        }
        
        if ($compressed) {
            $content = gzuncompress(fread($fResource, $info['length']));
        } else {
            $content = fread($fResource, $info['length']);
        }
        
        fclose($fResource);
        
        return $content;
    }
    
}