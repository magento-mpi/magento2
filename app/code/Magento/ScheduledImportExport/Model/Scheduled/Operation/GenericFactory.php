<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model\Scheduled\Operation;

class GenericFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create array optioned object
     *
     * @param string $model
     * @param array $data
     * @throws \InvalidArgumentException
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface
     */
    public function create($model, array $data = array())
    {
        $modelInstance = $this->_objectManager->create($model, $data);
        if (false ==
            $modelInstance instanceof \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface
        ) {
            throw new \InvalidArgumentException(
                $model .
                'doesn\'t implement \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface'
            );
        }
        return $modelInstance;
    }
}
