<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview\View;

interface StateInterface
{
    /**#@+
     * View modes
     */
    const MODE_ENABLED = 'enabled';
    const MODE_DISABLED = 'disabled';
    /**#@-*/

    /**#@+
     * View statuses
     */
    const STATUS_IDLE = 'idle';
    const STATUS_WORKING = 'working';
    const STATUS_SUSPENDED = 'suspended';
    /**#@-*/

    /**
     * Fill object with state data by view ID
     *
     * @param string $viewId
     * @return $this
     */
    public function loadByView($viewId);

    /**
     * Save state object
     *
     * @return \Magento\Mview\View\StateInterface
     * @throws \Exception
     */
    public function save();

    /**
     * Delete state object
     *
     * @return \Magento\Mview\View\StateInterface
     * @throws \Exception
     */
    public function delete();

    /**
     * Get state view ID
     *
     * @return string
     */
    public function getViewId();

    /**
     * Get state mode
     *
     * @return string
     */
    public function getMode();

    /**
     * Set state mode
     *
     * @param string $mode
     * @return \Magento\Mview\View\StateInterface
     */
    public function setMode($mode);

    /**
     * Get state status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set state status
     *
     * @param string $status
     * @return \Magento\Mview\View\StateInterface
     */
    public function setStatus($status);

    /**
     * Get state version ID
     *
     * @return string
     */
    public function getVersionId();

    /**
     * Set state version ID
     *
     * @param int $versionId
     * @return \Magento\Mview\View\StateInterface
     */
    public function setVersionId($versionId);

    /**
     * Get state updated time
     *
     * @return string
     */
    public function getUpdated();

    /**
     * Set state updated time
     *
     * @param string|int|\Zend_Date $updated
     * @return \Magento\Mview\View\StateInterface
     */
    public function setUpdated($updated);
}
