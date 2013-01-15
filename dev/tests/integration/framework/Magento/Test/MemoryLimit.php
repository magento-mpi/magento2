<?php
/**
 * A tool for limiting allowed memory usage and memory leaks
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_MemoryLimit
{
    /**
     * @var Magento_Test_Helper_Memory
     */
    private $_helper;

    /**
     * @var array
     */
    private $_orig = array();

    /**
     * @var array
     */
    private $_bytes = array();

    /**
     * Initialize with the values
     *
     * @param string $memCap
     * @param string $leakCap
     * @param Magento_Test_Helper_Memory $helper
     * @throws InvalidArgumentException
     */
    public function __construct($memCap, $leakCap, Magento_Test_Helper_Memory $helper)
    {
        $this->_orig = array($memCap, $leakCap);
        $this->_bytes[0] = $memCap ? $helper->convertToBytes($memCap) : 0;
        $this->_bytes[1] = $leakCap ? $helper->convertToBytes($leakCap) : 0;
        $this->_helper = $helper;
    }

    /**
     * Get a header printout
     *
     * @return string
     */
    public static function printHeader()
    {
        return PHP_EOL . '=== Memory Usage System Stats ===' . PHP_EOL;
    }

    /**
     * Get statistics printout
     *
     * @return string
     */
    public function printStats()
    {
        list($memCap, $leakCap) = $this->_bytes;
        list($usage, $leak) = $this->_getUsage();
        $result = array();

        $msg = "Memory usage:\t{$this->_toMebibytes($usage)}";
        $percentMsg = ' - %.2F%% of currently configured limit of %s';
        if ($memCap) {
            $msg .= sprintf($percentMsg, 100 * $usage / $memCap, $this->_orig[0]);
        }
        $result[] = "{$msg}.";

        $msg = "Estimated leak:\t{$this->_toMebibytes($leak)}";
        if ($leakCap) {
            $msg .= sprintf($percentMsg, 100 * $leak / $leakCap, $this->_orig[1]);
        }
        $msg .= sprintf(" - %.2F%% of memory usage", 100 * $leak / $usage);
        $result[] = "{$msg}.";
        $result[] = sprintf('Estimated "official" memory usage: %s.', $this->_toMebibytes($usage - $leak));

        return implode(PHP_EOL, $result) . PHP_EOL;
    }

    /**
     * Convert bytes to mebibytes (2^20)
     *
     * @param int $bytes
     * @return string
     */
    private function _toMebibytes($bytes)
    {
        return sprintf('%.2FMiB', $bytes / (1024 * 1024));
    }

    /**
     * Determine memory usage
     *
     * @return null
     * @throws LogicException
     */
    public function validateUsage()
    {
        list($memCap, $leakCap) = $this->_bytes;
        if (!$memCap && !$leakCap) {
            return null;
        }
        list($usage, $leak) = $this->_getUsage();
        if ($memCap && ($usage >= $memCap)) {
            throw new LogicException("Memory limit of {$this->_orig[0]} ({$memCap} bytes) has been reached.");
        }
        if ($leakCap && ($leak >= $leakCap)) {
            throw new LogicException(
                "Estimated memory leak limit of {$this->_orig[1]} ({$leakCap} bytes) has been reached."
            );
        }
    }

    /**
     * Usage/leak getter sub-routine
     *
     * @return array
     */
    private function _getUsage()
    {
        $usage = $this->_helper->getRealMemoryUsage();
        return array($usage, $usage - memory_get_usage(true));
    }
}
