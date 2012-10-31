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
    protected $_logs;

    CONST FILE_KEY_VALID = 'valid';
    CONST FILE_KEY_INVALID = 'invalid';

    public function __construct()
    {
        $this->_logs = array(
            self::FILE_KEY_VALID => array(),
            self::FILE_KEY_INVALID => array()
        );
    }

    /**
     * Add log data
     *
     * @param string $fileName
     * @param string $type
     * @return Tools_Migration_System_Configuration_LoggerAbstract
     */
    public function add($fileName, $type)
    {
        $this->_logs[$type][] = $fileName;
        return $this;
    }

    /**
     * Convert logger object to string
     *
     * @return string
     */
    public function __toString()
    {
        $result = array();
        $totalCount = 0;
        foreach ($this->_logs as $type => $data) {
            $countElements = count($data);
            $totalCount += $countElements;
            $total[] = $type . ': ' . $countElements;

            if (!$countElements) {
                continue;
            }

            $result[] = '------------------------------';
            $result[] =  $type . ':';
            foreach ($data as $fileName) {
                $result[] = $fileName;
            }
        }

        $total[] = 'Total: ' . $totalCount;
        $result = array_merge($total, $result);
        return implode(PHP_EOL, $result);
    }

    /**
     * Generate report
     *
     * @abstract
     */
    public abstract function report();
}
