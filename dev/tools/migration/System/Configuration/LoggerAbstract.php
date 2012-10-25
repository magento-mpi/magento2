<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * System configuration migration logger
 */
abstract class Tools_Migration_System_Configuration_LoggerAbstract
{
    /**
     * List of logs
     *
     * @var array
     */
    protected $_logs = array();

    CONST FILE_KEY_VALID = 'valid';
    CONST FILE_KEY_INVALID = 'invalid';

    /**
     * Add log data
     *
     * @param string $fileName
     * @param string $type
     * @return Tools_Migration_System_Configuration_LoggerAbstract
     */
    public function add($fileName, $type)
    {
        switch($type) {
            case self::FILE_KEY_VALID:
                $this->_logs[self::FILE_KEY_VALID][] = $fileName;
                break;
            case self::FILE_KEY_INVALID:
                $this->_logs[self::FILE_KEY_INVALID][] = $fileName;
                break;
        }
        return $this;
    }

    /**
     * Convert logger object to string
     *
     * @return string
     */
    public function __toString()
    {
        $countValidFiles = isset($this->_logs[self::FILE_KEY_VALID])?
            count($this->_logs[self::FILE_KEY_VALID]) : 0;
        $countInvalidFiles = isset($this->_logs[self::FILE_KEY_INVALID])?
            count($this->_logs[self::FILE_KEY_INVALID]) : 0;
        $totalFiles = $countInvalidFiles + $countValidFiles;

        $result[] = 'Total: '. $totalFiles;
        $result[] = 'Valid: '. $countValidFiles;
        $result[] = 'Invalid: '. $countInvalidFiles;

        if ($countInvalidFiles > 0) {
            $result[] = '------------------------------';
            $result[] = 'Invalid:';
            foreach ($this->_logs[self::FILE_KEY_INVALID] as $fileName) {
                $result[] = $fileName;
            }
        }

        if ($countValidFiles > 0) {
            $result[] = '------------------------------';
            $result[] = 'Valid:';
            foreach ($this->_logs[self::FILE_KEY_VALID] as $fileName) {
                $result[] = $fileName;
            }
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * Generate report
     *
     * @abstract
     * @return mixed
     */
    public abstract function report();
}
