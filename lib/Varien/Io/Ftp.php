<?php

/**
 * FTP client
 *
 * @copyright   2007 Varien Inc.
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Varien
 * @subpackage  Io
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Varien_Io_Ftp extends Varien_Io_Abstract
{
    const ERROR_EMPTY_HOST = 1;
    const ERROR_INVALID_CONNECTION = 2;
    const ERROR_INVALID_LOGIN = 3;
    const ERROR_INVALID_PATH = 4;
    const ERROR_INVALID_MODE = 5;
    const ERROR_INVALID_DESTINATION = 6;
    const ERROR_INVALID_SOURCE = 7;
    
    /**
     * Connection config
     *
     * @var array
     */
    protected $_config;
    
    /**
     * An FTP connection
     *
     * @var resource
     */
    protected $_conn;
    
    /**
     * Error code
     * 
     * @var int
     */
    protected $_error;
    
    /**
     * Open a connection
     *
     * Possible argument keys:
     * - host        required
     * - port        default 21
     * - timeout     default 90
     * - user        default anonymous
     * - password    default empty
     * - ssl         default no
     * - passive     default no
     * - path        default empty
     * - file_mode   default FTP_BINARY
     * 
     * @param array $args
     * @return boolean
     */
    public function open($args)
    {
        if (empty($args['host'])) {
            $this->_error = self::ERROR_EMPTY_HOST;
            return false;
        }
        
        if (empty($args['port'])) {
            $args['port'] = 21;
        }

        if (empty($args['user'])) {
            $args['user'] = 'anonymous';
            $args['password'] = 'anonymous@noserver.com';
        }

        if (empty($args['password'])) {
            $args['password'] = '';
        }
        
        if (empty($args['timeout'])) {
            $args['timeout'] = 90;
        }
        
        if (empty($args['file_mode'])) {
            $args['file_mode'] = FTP_BINARY;
        }
        
        $this->_config = $args;
        
        if (empty($this->_config['ssl'])) {
            $this->_conn = @ftp_connect($this->_config['host'], $this->_config['port'], $this->_config['timeout']);
        } else {
            $this->_conn = @ftp_ssl_connect($this->_config['host'], $this->_config['port'], $this->_config['timeout']);
        }
        if (!$this->_conn) {
            $this->_error = self::ERROR_INVALID_CONNECTION;
            return false;
        }
        
        if (!@ftp_login($this->_conn, $this->_config['user'], $this->_config['password'])) {
            $this->_error = self::ERROR_INVALID_LOGIN;
            $this->close();
            return false;
        }
        
        if (!empty($this->_config['path'])) {
            if (!@ftp_chdir($this->_conn, $this->_config['path'])) {
                $this->_error = self::ERROR_INVALID_PATH;
                $this->close();
                return false;
            }
        }
        
        if (!empty($this->_config['passive'])) {
            if (!@ftp_pasv($this->_conn, true)) {
                $this->_error = self::ERROR_INVALID_MODE;
                $this->close();
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Close a connection
     * 
     * @return boolean
     */
    public function close()
    {
        return @ftp_close($this->_conn);
    }
    
    /**
     * Create a directory
     *
     * @todo implement $mode and $recursive
     * @param string $dir
     * @param int $mode
     * @param boolean $recursive
     * @return boolean
     */
    public function mkdir($dir, $mode=0777, $recursive=true)
    {
        return @ftp_mkdir($this->_conn, $dir);
    }
    
    /**
     * Delete a directory
     *
     * @param string $dir
     * @return boolean
     */
    public function rmdir($dir)
    {
        return @ftp_rmdir($this->_conn, $dir);
    }
    
    /**
     * Get current working directory
     *
     * @return string
     */
    public function pwd()
    {
        return @ftp_pwd($this->_conn);
    }
    
    /**
     * Change current working directory
     *
     * @param string $dir
     * @return boolean
     */
    public function cd($dir)
    {
        return @ftp_chdir($this->_conn, $dir);
    }

    /**
     * Read a file to result, file or stream
     *
     * @param string $filename
     * @param string|resource $dest
     * @return string
     */
    public function read($filename, $dest=null)
    {
        if (is_string($dest)) {
            return @ftp_get($this->_conn, $dest, $filename, $this->_config['file_mode']);
        } else {
            if (is_resource($dest)) {
                $stream = $dest;
            } elseif (is_null($dest)) {
                ob_start();
                $stream = STDOUT;
            } else {
                $this->_error = self::ERROR_INVALID_DESTINATION;
                return false;
            }
            $result = @ftp_fget($this->_conn, $dest, $filename, $this->_config['file_mode']);
            if (is_null($dest)) {
                @fclose($stream);
                return ob_get_clean();
            } else {
                return $result;
            }
        }
    }
    
    /**
     * Write a file from string, file or stream
     *
     * @todo writing a string might not work
     * @todo does it work to read from stdout?.. if not, how to upload a string to ftp file without creating temporary file?
     * @param string $filename
     * @param string|resource $src
     * @return int|boolean
     */
    public function write($filename, $src, $mode=null)
    {
        if (is_string($src) && is_readable($src)) {
            return @ftp_put($this->_conn, $filename, $src, $mode);
        } else {
            if (is_string($src)) {
                ob_start();
                $stream = STDOUT;
                echo $src;
            } elseif (is_resource($src)) {
                $stream = $src;
            } else {
                return self::ERROR_INVALID_SOURCE;
                return false;
            }
            $result = @ftp_fput($this->_conn, $filename, $stream, $mode);
            if (is_string($src)) {
                ob_flush();
            }
            return $result;
        }
    }
    
    /**
     * Delete a file
     *
     * @param string $filename
     * @return boolean
     */
    public function rm($filename)
    {
        return @ftp_delete($this->_conn, $filename);
    }
    
    /**
     * Rename or move a directory or a file
     *
     * @param string $src
     * @param string $dest
     * @return boolean
     */
    public function mv($src, $dest)
    {
        return @ftp_rename($this->_conn, $src, $dest);
    }
    
    /**
     * Change mode of a directory or a file
     * 
     * @param string $filename
     * @param int $mode
     * @return boolean
     */
    public function chmod($filename, $mode)
    {
        return @ftp_chmod($this->_conn, $mode, $filename);
    }
}