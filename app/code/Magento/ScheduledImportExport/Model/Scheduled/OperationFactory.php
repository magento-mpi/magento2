<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ScheduledImportExport_Model_Scheduled_Operation_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create array optioned object
     *
     * @param string $model
     * @param array $data
     * @throws InvalidArgumentException
     * @return Magento_Core_Model_Option_ArrayInterface
     */
    public function create($model, array $data = array())
    {
        $modelInstance = $this->_objectManager->create($model, $data);
        if (false == ($modelInstance instanceof Magento_ScheduledImportExport_Model_Scheduled_Operation_Interface)) {
            throw new InvalidArgumentException(
                $model . 'doesn\'t implement Magento_ScheduledImportExport_Model_Scheduled_Operation_Interface'
            );
        }
        return $modelInstance;
    }
}
