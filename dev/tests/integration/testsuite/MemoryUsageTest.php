<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class MemoryUsageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Number of application reinitialization iterations to be conducted by tests
     */
    const ISOLATION_ITERATION_COUNT = 20;

    /**
     * Test that application reinitialization produces no memory leaks
     */
    public function testIsolationMemoryLeak()
    {
        $this->_deallocateUnusedMemory();
        $actualMemoryUsage = $this->_getRealMemoryUsage();
        for ($i = 0; $i < self::ISOLATION_ITERATION_COUNT; $i++) {
            Magento_Test_Bootstrap::getInstance()->reinitialize();
            $this->_deallocateUnusedMemory();
        }
        $actualMemoryUsage = $this->_getRealMemoryUsage() - $actualMemoryUsage;
        if ($actualMemoryUsage > 0) {
            $this->fail(sprintf(
                "Application isolation causes the memory leak of %u bytes per %u iterations.",
                $actualMemoryUsage,
                self::ISOLATION_ITERATION_COUNT
            ));
        }
    }

    /**
     * Force to immediately deallocate currently unused memory
     */
    protected function _deallocateUnusedMemory()
    {
        gc_collect_cycles();
    }

    /**
     * Retrieve the effective memory usage of the current process
     *
     * memory_get_usage() cannot be used because of the bug
     * @link https://bugs.php.net/bug.php?id=62467
     *
     * @return int Memory usage in bytes
     */
    protected function _getRealMemoryUsage()
    {
        $pid = posix_getpid();
        $shell = new Magento_Shell();
        try {
            // attempt to use the Unix command line interface
            $result = $this->_getUnixProcessMemoryUsage($shell, $pid);
        } catch (Magento_Exception $e) {
            // fall back to the Windows command line
            $result = $this->_getWinProcessMemoryUsage($shell, $pid);
        }
        return $result;
    }

    /**
     * Retrieve the current process' memory usage using Unix command line interface
     *
     * @param Magento_Shell $shell
     * @param int $pid
     * @return int Memory usage in bytes
     */
    protected function _getUnixProcessMemoryUsage(Magento_Shell $shell, $pid)
    {
        /**
         * @link http://linux.die.net/man/1/top
         *
         * Output format invariant:
         *   PID USER    PR  NI  VIRT  RES  SHR S %CPU %MEM    TIME+  COMMAND
         * 12345 root    20   0  215m  36m  10m S   98  0.5   0:32.96 php
         */
        $output = $shell->execute('top -p %s -n 1 -b | grep PID -A 1', array($pid));

        $output = preg_split('/\n+/', $output, -1, PREG_SPLIT_NO_EMPTY);
        $keys = preg_split('/\s+/', $output[0], -1, PREG_SPLIT_NO_EMPTY);
        $values = preg_split('/\s+/', $output[1], -1, PREG_SPLIT_NO_EMPTY);
        $stats = array_combine($keys, $values);

        $result = $stats['RES']; // resident set size, the non-swapped physical memory

        if (is_numeric($result)) {
            $result .= 'k'; // kilobytes by default
        }

        return $this->_convertToBytes($result);
    }

    /**
     * Retrieve the current process' memory usage using Windows command line interface
     *
     * @param Magento_Shell $shell
     * @param int $pid
     * @return int Memory usage in bytes
     */
    protected function _getWinProcessMemoryUsage(Magento_Shell $shell, $pid)
    {
        /**
         * @link http://technet.microsoft.com/en-us/library/bb491010.aspx
         *
         * Output format invariant:
         * "Image Name","PID","Session Name","Session#","Mem Usage"
         * "svchost.exe","464","Services","0","61,252 K"
         */
        $output = $shell->execute('tasklist /fi %s /fo CSV', array("PID eq $pid"));

        /** @link http://www.php.net/manual/en/wrappers.data.php */
        $csvStream = 'data://text/plain;base64,' . base64_encode($output);
        $csvHandle = fopen($csvStream, 'r');
        $keys = fgetcsv($csvHandle);
        $values = fgetcsv($csvHandle);
        fclose($csvHandle);

        $stats = array_combine($keys, $values);

        $result = $stats['Mem Usage'];
        $result = str_replace(array('.', ',', ' '), '', $result);

        return $this->_convertToBytes($result);
    }

    /**
     * Convert a number optionally followed by the unit symbol (B, K, M, G, etc.) to bytes
     *
     * @param string $number String representation of a number
     * @return int
     * @throws InvalidArgumentException
     */
    protected function _convertToBytes($number)
    {
        $number = strtoupper($number);
        $units = 'BKMGTPEZY';
        if (!preg_match("/^(\d+)([$units]?)$/", $number, $matches)) {
            throw new InvalidArgumentException('Number format is not recognized.');
        }
        $result = (int)$matches[1];
        $unitSymbol = $matches[2];
        $unitShift = $unitSymbol ? strpos($units, $unitSymbol) * 10 : 0;
        $result = $result << $unitShift;
        return $result;
    }
}
