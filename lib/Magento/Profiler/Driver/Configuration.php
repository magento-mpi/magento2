<?php
/**
 * Profiler driver configuration.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Configuration
{
    /**
     * @#+
     * Specific configuration option names
     */
    const TYPE_OPTION = 'type';
    const BASE_DIR_OPTION = 'baseDir';
    /**@#-*/

    /**
     * @var array
     */
    protected $_data = null;

    /**
     * @param array|null $data
     */
    public function __construct(array $data = null)
    {
        $this->_data = $data;
    }

    /**
     * Get "object type" option value
     *
     * @param string $defaultType
     * @return string
     */
    public function getTypeValue($defaultType = null)
    {
        return $this->getStringValue(self::TYPE_OPTION, $defaultType);
    }

    /**
     * Set "object type" option value
     *
     * @param string $driverType
     */
    public function setTypeValue($driverType)
    {
        $this->setValue(self::TYPE_OPTION, $driverType);
    }

    /**
     * Is "object type" option has a value
     *
     * @return bool
     */
    public function hasTypeValue()
    {
        return $this->hasValue(self::TYPE_OPTION);
    }

    /**
     * Get "base directory" option value
     *
     * @param string $defaultBaseDir
     * @return string
     */
    public function getBaseDirValue($defaultBaseDir = null)
    {
        return $this->getStringValue(self::BASE_DIR_OPTION, $defaultBaseDir);
    }

    /**
     * Set "base directory" option value
     *
     * @param string $baseDir
     */
    public function setBaseDirValue($baseDir)
    {
        $this->setValue(self::BASE_DIR_OPTION, $baseDir);
    }

    /**
     * Is "base directory" option has a value.
     *
     * @return bool
     */
    public function hasBaseDirValue()
    {
        return $this->hasValue(self::BASE_DIR_OPTION);
    }

    /**
     * Set option value.
     *
     * @param string $name
     * @param mixed $value
     */
    public function setValue($name, $value)
    {
        $this->_data[$name] = $value;
    }

    /**
     * Is $name option has a value.
     *
     * @param string $name
     * @return bool
     */
    public function hasValue($name)
    {
        return isset($this->_data[$name]);
    }


    /**
     * Get $name option value as scalar, using $default if value is undefined or not scalar.
     *
     * @param string $name
     * @param mixed $default
     * @return int|float|bool|null
     */
    public function getScalarValue($name, $default = null)
    {
        $value = $this->getValue($name, $default);
        if (is_scalar($value)) {
            return $value;
        } else {
            return $default;
        }
    }
    /**
     * Get $name option raw value, using $default if value is undefined.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getValue($name, $default = null)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        } else {
            return $default;
        }
    }

    /**
     * Get $name option value as integer, using $default if value is undefined or not numeric.
     *
     * @param string $name
     * @param mixed $default
     * @return int
     */
    public function getIntegerValue($name, $default = 0)
    {
        $value = $this->getValue($name, $default);
        if (is_numeric($value)) {
            return (int)$value;
        } else {
            return $default;
        }
    }

    /**
     * Get $name option value as float, using $default if value is undefined or not numeric.
     *
     * @param string $name
     * @param mixed $default
     * @return float
     */
    public function getFloatValue($name, $default = 0.0)
    {
        $value = $this->getValue($name, $default);
        if (is_numeric($value)) {
            return (float)$value;
        } else {
            return $default;
        }
    }

    /**
     * Get $name option value as boolean, using $default if value is undefined.
     *
     * @param string $name
     * @param mixed $default
     * @return bool
     */
    public function getBoolValue($name, $default = false)
    {
        return (bool)$this->getValue($name, $default);
    }

    /**
     * Get $name option value as string, using $default if value is undefined or not scalar
     * and not convertable to string.
     *
     * @param string $name
     * @param mixed $default
     * @return string
     */
    public function getStringValue($name, $default = '')
    {
        $value = $this->getValue($name, $default);
        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            return (string)$value;
        } else {
            return $default;
        }
    }

    /**
     * Get $name option value as array, using $default if value is undefined or array.
     *
     * @param string $name
     * @param mixed $default
     * @return array
     */
    public function getArrayValue($name, array $default = array())
    {
        $value = $this->getValue($name, $default);
        if (is_array($value)) {
            return $value;
        } else {
            return $default;
        }
    }
}
