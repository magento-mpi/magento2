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
 * Link Tracker model
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_LinkTracker extends Mage_Core_Model_Abstract
{
    /**
     * Url Builder
     *
     * @var Mage_Core_Model_Url
     */
    protected $_urlBuilder;

    /**
     * Prefix for model event names
     *
     * @var string
     */
    protected $_eventPrefix = 'launcher_link_tracker';


    /**
     * Class constructor
     *
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_Cache $cacheManager
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param Mage_Core_Model_Url $urlBuilder
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Mage_Core_Model_Url $urlBuilder,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($eventDispatcher, $cacheManager, $resource, $resourceCollection, $data);
        $this->_urlBuilder = $urlBuilder;
        $this->_init('Mage_Launcher_Model_Resource_LinkTracker');
    }

    public function renderUrl()
    {
        echo $this->_urlBuilder->getUrl('launcher/redirect',  array('id' => $this->getId()));
    }

}
