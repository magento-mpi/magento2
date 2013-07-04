<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache grid collection
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PageCache_Model_Resource_Grid_Collection extends Varien_Data_Collection
{
    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @param Mage_Core_Model_App $app
     */
    public function __construct(Mage_Core_Model_App $app)
    {
        $this->_app = $app;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_PageCache_Model_Resource_Grid_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            foreach ($this->_app->getCacheInstance()->getTypes() as $type) {
                $this->addItem($type);
            }
            $this->_setIsLoaded(true);
        }
        return $this;
    }
}
