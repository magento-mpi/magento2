<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Io
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Io;

/**
 * FTP client
 *
 * @category   Magento
 * @package    Magento_Io
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ftp extends AbstractIo
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
     * @var string
     */
    protected $_tmpFilename;

    /**
     * Open a connection
     *
     * Possible argument keys:
     * - host        required
     * - port        default 21
     * - timeout     default 90
     * - user        default anonymous
     * - password    default empty
     * - ssl         default false
     * - passive     default false
     * - path        default empty
     * - file_mode   default FTP_BINARY
     *
     * @param array $args
     * @return true
     * @throws IoException
     */
    public function open(array $args=array())
    {
        if (empty($args['host'])) {
            $this->_error = self::ERROR_EMPTY_HOST;
            throw new IoException('Empty host specified');
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
            throw new IoException('Could not establish FTP connection, invalid host or port');
        }

        if (!@ftp_login($this->_conn, $this->_config['user'], $this->_config['password'])) {
            $this->_error = self::ERROR_INVALID_LOGIN;
            $this->close();
            throw new IoException('Invalid user name or password');
        }

        if (!empty($this->_config['path'])) {
            if (!@ftp_chdir($this->_conn, $this->_config['path'])) {
                $this->_error = self::ERROR_INVALID_PATH;
                $this->close();
                throw new IoException('Invalid path');
            }
        }

        if (!empty($this->_config['passive'])) {
            if (!@ftp_pasv($this->_conn, true)) {
                $this->_error = self::ERROR_INVALID_MODE;
                $this->close();
                throw new IoException('Invalid file transfer mode');
            }
        }

        return true;
    }

    /**
     * Close a connection
     *
     * @return bool
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
     * @param bool $recursive
     * @return bool
     */
    public function mkdir($dir, $mode=0777, $recursive=true)
    {
        return @ftp_mkdir($this->_conn, $dir);
    }

    /**
     * Delete a directory
     *
     * @param string $dir
     * @param bool $recursive
     * @return bool
     */
    public function rmdir($dir, $recursive=false)
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
     * @return bool
     */
    public function cd($dir)
    {
        return @ftp_chdir($this->_conn, $dir);
    }

    /**
     * Read a file to result, file or stream
     *
     * @param string $filename
     * @param string|resource|null $dest destination file name, stream, or if null will return file contents
     * @return false|string
     */
    public function read($filename, $dest=null)
    {
        if (is_string($dest)) {
            $result = ftp_get($this->_conn, $dest, $filename, $this->_config['file_mode']);
        } else {
            if (is_resource($dest)) {
                $stream = $dest;
            } elseif (is_null($dest)) {
                $stream = tmpfile();
            } else {
                $this->_error = self::ERROR_INVALID_DESTINATION;
                return false;
            }

            $result = ftp_fget($this->_conn, $stream, $filename, $this->_config['file_mode']);

            if (is_null($dest)) {
                fseek($stream, 0);
                $result = '';
                for ($result = ''; $s = fread($stream, 4096); $result .= $s);
                fclose($stream);
            }
        }
        return $result;
    }

    /**
     * Write a file from string, file or stream
     *
     * @param string $filename
     * @param string|resource $src filename, string data or source stream
     * @param null $mode
     * @return bool
     */
    public function write($filename, $src, $mode=null)
    {
        if (is_string($src) && is_readable($src)) {
            return @ftp_put($this->_conn, $filename, $src, $this->_config['file_mode']);
        } else {
            if (is_string($src)) {
                $stream = tmpfile();
                fputs($stream, $src);
                fseek($stream, 0);
            } elseif (is_resource($src)) {
                $stream = $src;
            } else {
                $this->_error = self::ERROR_INVALID_SOURCE;
                return false;
            }

            $result = ftp_fput($this->_conn, $filename, $stream, $this->_config['file_mode']);
            if (is_string($src)) {
                fclose($stream);
            }
            return $result;
        }
    }

    /**
     * Delete a file
     *
     * @param string $filename
     * @return bool
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
     * @return bool
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
     * @return bool
     */
    public function chmod($filename, $mode)
    {
        return @ftp_chmod($this->_conn, $mode, $filename);
    }

    /**
     * @param null $grep ignored parameter
     * @return array
     */
    public function ls($grep=null)
    {
        $ls = @ftp_nlist($this->_conn, '.');

        $list = array();
        foreach ($ls as $file) {
            $list[] = array(
                'text'=>$file,
                'id'=>$this->pwd().'/'.$file,
            );
        }

        return $list;
    }

    /**
     * @param bool $new
     * @return string
     */
    protected function _tmpFilename($new=false)
    {
        if ($new || !$this->_tmpFilename) {
            $this->_tmpFilename = tempnam( md5(uniqid(rand(), TRUE)), '' );
        }
        return $this->_tmpFilename;
    }
}
