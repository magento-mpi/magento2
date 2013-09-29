<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Custom handlers for models logging
 */
namespace Magento\Logging\Model\Handler;

class Models
{
    /**
     * Factory for event changes model
     *
     * @var \Magento\Logging\Model\Event\ChangesFactory
     */
    protected $_eventChangesFactory;

    /**
     * Construct
     *
     * @param \Magento\Logging\Model\Event\ChangesFactory $eventChangesFactory
     */
    public function __construct(\Magento\Logging\Model\Event\ChangesFactory $eventChangesFactory)
    {
        $this->_eventChangesFactory = $eventChangesFactory;
    }

    /**
     * SaveAfter handler
     *
     * @param object \Magento\Core\Model\AbstractModel $model
     * @return object \Magento\Logging\Event\Changes or false if model wasn't modified
     */
    public function modelSaveAfter($model, $processor)
    {
        $processor->collectId($model);
        /** @var \Magento\Logging\Model\Event\Changes $changes */
        $changes = $this->_eventChangesFactory->create();
        $changes->setOriginalData($model->getOrigData())
            ->setResultData($model->getData());
        return $changes;
    }

    /**
     * Delete after handler
     *
     * @param object \Magento\Core\Model\AbstractModel $model
     * @return object \Magento\Logging\Event\Changes
     */
    public function modelDeleteAfter($model, $processor)
    {
        $processor->collectId($model);
        /** @var \Magento\Logging\Model\Event\Changes $changes */
        $changes = $this->_eventChangesFactory->create();
        $changes->setOriginalData($model->getOrigData())
            ->setResultData(null);
        return $changes;
    }

    /**
     * MassUpdate after handler
     *
     * @param object \Magento\Core\Model\AbstractModel $model
     * @return object \Magento\Logging\Event\Changes
     */
    public function modelMassUpdateAfter($model, $processor)
    {
        return $this->modelSaveAfter($model, $processor);
    }

    /**
     * MassDelete after handler
     *
     * @param object \Magento\Core\Model\AbstractModel $model
     * @return object \Magento\Logging\Event\Changes
     */
    public function modelMassDeleteAfter($model, $processor)
    {
        return $this->modelSaveAfter($model, $processor);
    }

    /**
     * Load after handler
     *
     * @param object \Magento\Core\Model\AbstractModel $model
     * @return \Magento\Logging\Model\Event\Changes
     */
    public function modelViewAfter($model, $processor)
    {
        $processor->collectId($model);
        return true;
    }
}
