<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Mview\View;

/**
 * @method \Magento\Indexer\Model\Indexer\State setViewId(string $value)
 */
class State extends \Magento\Core\Model\AbstractModel implements \Magento\Mview\View\StateInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mview_state';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'mview_state';

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\Resource\Mview\View\State $resource
     * @param \Magento\Core\Model\Resource\Mview\View\State\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\Resource\Mview\View\State $resource,
        \Magento\Core\Model\Resource\Mview\View\State\Collection $resourceCollection,
        array $data = array()
    ) {
        if (!isset($data['mode'])) {
            $data['mode'] = self::MODE_DISABLED;
        }
        if (!isset($data['status'])) {
            $data['status'] = self::STATUS_IDLE;
        }
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Fill object with state data by view ID
     *
     * @param string $viewId
     * @return \Magento\Mview\View\StateInterface
     */
    public function loadByView($viewId)
    {
        $this->load($viewId, 'view_id');
        if (!$this->getId()) {
            $this->setViewId($viewId);
        }
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Core\Model\Mview\View\State
     */
    protected function _beforeSave()
    {
        $this->setUpdated(time());
        return parent::_beforeSave();
    }

    /**
     * Get state view ID
     *
     * @return string
     */
    public function getViewId()
    {
        return $this->getData('view_id');
    }

    /**
     * Get state mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getData('mode');
    }

    /**
     * Set state mode
     *
     * @param string $mode
     * @return \Magento\Mview\View\StateInterface
     */
    public function setMode($mode)
    {
        $this->setData('mode', $mode);
        return $this;
    }

    /**
     * Get state status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * Set state status
     *
     * @param string $status
     * @return \Magento\Mview\View\StateInterface
     */
    public function setStatus($status)
    {
        $this->setData('status', $status);
        return $this;
    }

    /**
     * Get state updated time
     *
     * @return string
     */
    public function getUpdated()
    {
        return $this->getData('updated');
    }

    /**
     * Set state updated time
     *
     * @param string|int|\Zend_Date $updated
     * @return \Magento\Mview\View\StateInterface
     */
    public function setUpdated($updated)
    {
        $this->setData('updated', $updated);
        return $this;
    }

    /**
     * Get state version ID
     *
     * @return string
     */
    public function getVersionId()
    {
        return $this->getData('version_id');
    }

    /**
     * Set state version ID
     *
     * @param int $versionId
     * @return \Magento\Mview\View\StateInterface
     */
    public function setVersionId($versionId)
    {
        $this->setData('version_id', $versionId);
        return $this;
    }
}
