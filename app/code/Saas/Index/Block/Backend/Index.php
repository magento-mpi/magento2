<?php
/**
 * Block class for search index refresh
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Block_Backend_Index extends Mage_Backend_Block_Widget_Container
{
    /**
     * Milliseconds for check task status in queue
     */
    const TASK_TIME_CHECK = 2500;

    /**
     * Initialize "controller"
     */
    protected function _construct()
    {
        $this->_addButton('refresh_index', array(
            'label'    => $this->__('Refresh Index'),
            'class'    => 'refresh',
            'onclick'  => 'return goIndex.refreshIndex()',
            'disabled' => $this->isTaskAdded() ? 'disabled' : '',
        ), 0, 0, 'refresh');

        parent::_construct();
    }

    /**
     * Get url for put index into queue
     *
     * @return string
     */
    public function getRefreshIndexUrl()
    {
        return $this->getUrl('adminhtml/saas_index/refresh');
    }

    /**
     * Get url for delete task from queue
     *
     * @return string
     */
    public function getCancelIndexUrl()
    {
        return $this->getUrl('adminhtml/saas_index/cancel');
    }

    /**
     * Get url for put index into queue
     *
     * @return string
     */
    public function getUpdateStatusUrl()
    {
        return $this->getUrl('adminhtml/saas_index/updateStatus');
    }

    /**
     * Dummy method! Check is task added into the queue
     *
     * @return bool
     */
    public function isTaskAdded()
    {
        return $this->isTaskProcessing() || isset($_GET['added']);
    }

    /**
     * Dummy method!  Check is task currently is processing
     *
     * @return bool
     */
    public function isTaskProcessing()
    {
        return isset($_GET['processing']);
    }

    /**
     * Return milliseconds for check task status in queue
     *
     * @return int
     */
    public function getTaskCheckTime()
    {
        return self::TASK_TIME_CHECK;
    }
}
