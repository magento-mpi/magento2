<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Convenient access to the bootstrap settings
 */
class Magento_Test_Bootstrap_Settings
{
    /**
     * Base directory to be used to resolve relative paths
     *
     * @var string
     */
    private $_baseDir;

    /**
     * Key-value pairs of the settings
     *
     * @var array
     */
    private $_settings = array();

    /**
     * Constructor
     *
     * @param string $baseDir
     * @param array $settings
     */
    public function __construct($baseDir, array $settings)
    {
        $this->_baseDir = $baseDir;
        $this->_settings = $settings;
    }

    /**
     * Retrieve a setting interpreting it as a scalar value
     *
     * @param string $settingName
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getScalarValue($settingName, $defaultValue = null)
    {
        return (!empty($this->_settings[$settingName]) ? $this->_settings[$settingName] : $defaultValue);
    }

    /**
     * Whether a setting is enabled or not, if interpreted as a boolean value
     *
     * @param string $settingName
     * @return bool
     */
    public function isEnabled($settingName)
    {
        return ($this->getScalarValue($settingName) === 'enabled');
    }

    /**
     * Retrieve a setting interpreting it as a relative file name value
     *
     * @param string $settingName
     * @param mixed $defaultValue
     * @return mixed|string
     */
    public function getFileValue($settingName, $defaultValue = null)
    {
        $result = $this->getScalarValue($settingName, $defaultValue);
        if ($result) {
            $result = $this->_getAbsolutePath($result);
        }
        return $result;
    }

    /**
     * Retrieve a setting interpreting it as a semicolon-separated glob patterns
     *
     * @param string $settingName
     * @param string $defaultValue
     * @return array
     */
    public function getPathPatternValue($settingName, $defaultValue)
    {
        $result = $this->getScalarValue($settingName, $defaultValue);
        if ($result) {
            $result = $this->_resolvePathPattern($result);
        }
        return $result;
    }

    /**
     * Retrieve array of absolute config file names
     *
     * @param string $settingName
     * @param mixed $defaultValue
     * @param array $extraConfigFiles
     * @return array
     */
    public function getConfigFiles($settingName, $defaultValue, array $extraConfigFiles = array())
    {
        $result = array();
        $primaryConfigFile = $this->getFileValue($settingName, $defaultValue);
        if (!is_file($primaryConfigFile) && substr($primaryConfigFile, -5) != '.dist') {
            $primaryConfigFile .= '.dist';
        }
        $result[] = $primaryConfigFile;
        foreach ($extraConfigFiles as $extraConfigFile) {
            $extraConfigFile = $this->_getAbsolutePath($extraConfigFile);
            if (is_file($extraConfigFile)) {
                $result[] = $extraConfigFile;
            }
        }
        return $result;
    }

    /**
     * Retrieve absolute path based on the relative one
     *
     * @param string $path
     * @return string
     */
    protected function _getAbsolutePath($path)
    {
        return $this->_baseDir . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Resolve semicolon-separated glob pattern(s) to the absolute paths
     *
     * @param string $pattern
     * @return array
     */
    protected function _resolvePathPattern($pattern)
    {
        $result = array();
        $allPatterns = preg_split('/\s*;\s*/', trim($pattern), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($allPatterns as $onePattern) {
            $onePattern = $this->_getAbsolutePath($onePattern);
            $files = glob($onePattern, GLOB_BRACE);
            $result = array_merge($result, $files);
        }
        return $result;
    }
}
