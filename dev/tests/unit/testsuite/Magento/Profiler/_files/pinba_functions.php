<?php
/**
 * Pinba functions emulation
 *
 * @copyright {}
 */
if (!extension_loaded('pinba') || !function_exists('pinba_timer_start')) {
    /**
     * Placeholder for pinba_timer_start
     *
     * @param array $tags
     * @return mixed
     */
    function pinba_timer_start($tags)
    {
        return 'resource_' . count($tags) . '_' . mt_rand(0, 1000);
    }

    /**
     * Placeholder for pinba_timer_stop
     *
     * @param mixed $resource
     * @return mixed
     */
    function pinba_timer_stop($resource)
    {
        return $resource;
    }

    /**
     * Placeholder for pinba_timer_delete
     *
     * @param mixed $resource
     * @return mixed
     */
    function pinba_timer_delete($resource)
    {
        return $resource;
    }
}
