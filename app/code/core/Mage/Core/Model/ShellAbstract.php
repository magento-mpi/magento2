<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shell scripts abstract class
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_ShellAbstract
{
    /**
     * Raw arguments, that should be parsed
     *
     * @var array
     */
    protected $_rawArgs     = array();

    /**
     * Parsed input arguments
     *
     * @var array
     */
    protected $_args        = array();

    /**
     * Entry point - script filename that is executed
     *
     * @var string
     */
    protected $_entryPoint = null;

    /**
     * Initializes application and parses input parameters
     *
     * @var string $entryPoint
     */
    public function __construct($entryPoint)
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            throw new Exception('This script cannot be run from Browser. This is the shell script.');
        }

        $this->_entryPoint = $entryPoint;
        $this->_rawArgs = $_SERVER['argv'];
        $this->_applyPhpVariables();
        $this->_parseArgs();
    }

    /**
     * Sets raw arguments to be parsed
     *
     * @param array $args
     * @return Mage_Core_Model_ShellAbstract
     */
    public function setRawArgs($args)
    {
        $this->_rawArgs = $args;
        $this->_parseArgs();
        return $this;
    }


    /**
     * Gets Magento root path (with last directory separator)
     *
     * @return string
     */
    protected function _getRootPath()
    {
        return Mage::getBaseDir() . '/../';
    }

    /**
     * Parses .htaccess file and apply php settings to shell script
     *
     * @return Mage_Core_Model_ShellAbstract
     */
    protected function _applyPhpVariables()
    {
        $htaccess = $this->_getRootPath() . '.htaccess';
        if (file_exists($htaccess)) {
            // parse htaccess file
            $data = file_get_contents($htaccess);
            $matches = array();
            preg_match_all('#^\s+?php_value\s+([a-z_]+)\s+(.+)$#siUm', $data, $matches, PREG_SET_ORDER);
            if ($matches) {
                foreach ($matches as $match) {
                    @ini_set($match[1], str_replace("\r", '', $match[2]));
                }
            }
            preg_match_all('#^\s+?php_flag\s+([a-z_]+)\s+(.+)$#siUm', $data, $matches, PREG_SET_ORDER);
            if ($matches) {
                foreach ($matches as $match) {
                    @ini_set($match[1], str_replace("\r", '', $match[2]));
                }
            }
        }
        return $this;
    }

    /**
     * Parses input arguments
     *
     * @return Mage_Core_Model_ShellAbstract
     */
    protected function _parseArgs()
    {
        $current = null;
        foreach ($this->_rawArgs as $arg) {
            $match = array();
            if (preg_match('#^--([\w\d_-]{1,})$#', $arg, $match) || preg_match('#^-([\w\d_]{1,})$#', $arg, $match)) {
                $current = $match[1];
                $this->_args[$current] = true;
            } else {
                if ($current) {
                    $this->_args[$current] = $arg;
                } else if (preg_match('#^([\w\d_]{1,})$#', $arg, $match)) {
                    $this->_args[$match[1]] = true;
                }
            }
        }
        return $this;
    }

    /**
     * Runs script
     *
     * @return Mage_Core_Model_ShellAbstract
     */
    abstract public function run();

    /**
     * Shows usage help, if requested
     *
     * @return bool
     */
    protected function _showHelp()
    {
        if (isset($this->_args['h']) || isset($this->_args['help'])) {
            echo $this->getUsageHelp();
            return true;
        }
        return false;
    }

    /**
     * Retrieves usage help message
     *
     * @return string
     */
    public function getUsageHelp()
    {
        return <<<USAGE
Usage:  php -f {$this->_entryPoint} -- [options]

  -h            Short alias for help
  help          This help
USAGE;
    }

    /**
     * Retrieves argument value by name. If argument is not found - returns FALSE.
     *
     * @param string $name the argument name
     * @return mixed
     */
    public function getArg($name)
    {
        if (isset($this->_args[$name])) {
            return $this->_args[$name];
        }
        return false;
    }
}