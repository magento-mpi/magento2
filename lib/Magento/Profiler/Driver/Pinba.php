<?php
/**
 * Pinba profiler driver.
 *
 * @copyright {}
 */
class Saas_Pinba_Model_Profiler_Pinba implements Magento_Profiler_DriverInterface
{
    const TIMER_NAME_TAG = 'timerId';

    protected $_isEnabled = false;

    /**
     * Array with started timers
     *
     * @var array
     */
    protected $_timersStarted = array();

    /**
     * Get tags with timer id included.
     *
     * @param string $timerId
     * @param array|null $tags
     * @return array
     */
    protected function _getTagsWithTimerId($timerId, array $tags = null)
    {
        return array_merge(array(self::TIMER_NAME_TAG => $timerId), $tags);
    }

    /**
     * Enable profiling.
     */
    public function enable()
    {
        $this->_isEnabled = true;
    }

    /**
     * Disabled profiling.
     */
    public function disable()
    {
        $this->_isEnabled = false;
    }

    /**
     * Start timer
     *
     * @param string $timerId
     * @param array $tags
     */
    public function start($timerId, array $tags = null)
    {
        if ($this->_isEnabled) {
            $tags = $this->_getTagsWithTimerId($timerId, $tags);
            $this->_timersStarted[$timerId] = pinba_timer_start($tags);
        }
    }

    /**
     * Stop timer for given key.
     *
     * @param null|string $timerId
     * @param array $tags
     */
    public function stop($timerId = null, array $tags = null)
    {
        if ($this->_isEnabled) {
            if (is_null($timerId)) {
                foreach (array_keys($this->_timersStarted) as $startedTimerId) {
                    $this->stop($startedTimerId);
                }
            } elseif (isset($this->_timersStarted[$timerId])) {
                pinba_timer_stop($this->_timersStarted[$timerId]);
            }
        }
    }

    /**
     * Reset collected statistics for specified timer or for whole profiler if timer name is omitted.
     *
     * @param string|null $timerId
     *
     * @return mixed
     */
    public function reset($timerId = null)
    {
        if ($this->_isEnabled) {
            if (is_null($timerId)) {
                foreach (array_keys($this->_timersStarted) as $startedTimerId) {
                    $this->reset($startedTimerId);
                }
            } elseif (isset($this->_timersStarted[$timerId])) {
                pinba_timer_delete($this->_timersStarted[$timerId]);
            }
        }
    }
}
