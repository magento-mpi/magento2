<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Launcher page collection
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Resource_Page_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Page factory
     *
     * @var Mage_Launcher_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * Class constructor
     *
     * @param Mage_Launcher_Model_PageFactory $pageFactory
     * @param Mage_Core_Model_Resource_Db_Abstract|null $resource
     */
    public function __construct(
        Mage_Launcher_Model_PageFactory $pageFactory,
        Mage_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($resource);
        $this->_pageFactory = $pageFactory;
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Mage_Launcher_Model_Page', 'Mage_Launcher_Model_Resource_Page');
    }

    /**
     * Retrieve new page instance
     *
     * @return Mage_Launcher_Model_Page
     */
    public function getNewEmptyItem()
    {
        return $this->_pageFactory->create();
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return Mage_Launcher_Model_Resource_Page_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->walk('afterLoad');
        return $this;
    }
}
