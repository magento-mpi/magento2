<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ScheduledImportExport_Model_Scheduled_Operation_GenericFactory
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
     * @return Magento_ScheduledImportExport_Model_Scheduled_Operation_Interface
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
