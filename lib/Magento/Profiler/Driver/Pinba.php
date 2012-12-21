<?php
/**
 * Pinba profiler driver.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Pinba implements Magento_Profiler_DriverInterface
{
    const TIMER_NAME_TAG = 'timerId';

    /**
     * Array with started timers
     *
     * @var array
     */
    protected $_startedTimers = array();

    /**
     * @var int
     */
    protected $_separatorLength = 0;

    /**
     * Initialize separator length
     *
     * @param array|null $configuration
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $configuration = null)
    {
        $this->_separatorLength = strlen(Magento_Profiler::NESTING_SEPARATOR);
    }

    /**
     * Get tags with timer id included.
     *
     * @param string $timerId
     * @param array|null $tags
     * @return array
     */
    protected function _getTagsWithTimerId($timerId, array $tags = null)
    {
        $pos = strrpos($timerId, Magento_Profiler::NESTING_SEPARATOR);
        if ($pos !== false) {
            $timerId = substr($timerId, $pos + $this->_separatorLength);
        }
        return array(self::TIMER_NAME_TAG => substr($timerId, 0, 64)) + (array)$tags;
    }

    /**
     * Start timer
     *
     * @param string $timerId
     * @param array $tags
     */
    public function start($timerId, array $tags = null)
    {
        $tags = $this->_getTagsWithTimerId($timerId, $tags);
        $this->_startedTimers[$timerId] = pinba_timer_start($tags);
    }

    /**
     * Stop timer for given key.
     *
     * @param null $timerId
     */
    public function stop($timerId)
    {
        if (isset($this->_startedTimers[$timerId])) {
            pinba_timer_stop($this->_startedTimers[$timerId]);
            unset($this->_startedTimers[$timerId]);
        }
    }

    /**
     * Clear collected statistics for specified timer or for whole profiler if timer id is omitted.
     *
     * @param string|null $timerId
     *
     * @return mixed
     */
    public function clear($timerId = null)
    {
        if (is_null($timerId)) {
            foreach (array_keys($this->_startedTimers) as $startedTimerId) {
                $this->clear($startedTimerId);
            }
        } elseif (isset($this->_startedTimers[$timerId])) {
            pinba_timer_delete($this->_startedTimers[$timerId]);
            unset($this->_startedTimers[$timerId]);
        }
    }
}
