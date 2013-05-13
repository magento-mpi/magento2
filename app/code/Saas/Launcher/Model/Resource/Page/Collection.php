<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Launcher page collection
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Resource_Page_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Page factory
     *
     * @var Saas_Launcher_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * Class constructor
     *
     * @param Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Saas_Launcher_Model_PageFactory $pageFactory
     * @param Mage_Core_Model_Resource_Db_Abstract|null $resource
     */
    public function __construct(
        Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Saas_Launcher_Model_PageFactory $pageFactory,
        Mage_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($fetchStrategy, $resource);
        $this->_pageFactory = $pageFactory;
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Saas_Launcher_Model_Page', 'Saas_Launcher_Model_Resource_Page');
    }

    /**
     * Retrieve new page instance
     *
     * @return Saas_Launcher_Model_Page
     */
    public function getNewEmptyItem()
    {
        return $this->_pageFactory->create();
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return Saas_Launcher_Model_Resource_Page_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->walk('afterLoad');
        return $this;
    }
}
