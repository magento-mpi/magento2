<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Indexer factory
 */
class Magento_Index_Model_Indexer_Factory
{
    /**
     * @var Magento_ObjectManager|null
     */
    protected $_objectManager = null;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $indexerInstanceName
     * @return Magento_Index_Model_Indexer_Abstract|null
     */
    public function create($indexerInstanceName)
    {
        if ($indexerInstanceName) {
            return $this->_objectManager->create($indexerInstanceName);
        }

        return null;
    }
}
