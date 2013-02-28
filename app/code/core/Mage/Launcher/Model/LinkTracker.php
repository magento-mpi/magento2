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
     * @param Mage_Core_Model_Context $context
     * @param Mage_Core_Model_Url $urlBuilder
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Mage_Core_Model_Url $urlBuilder,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_urlBuilder = $urlBuilder;
        $this->_init('Mage_Launcher_Model_Resource_LinkTracker');
    }

    /**
     * Render URL, which points to launcher redirect controller
     *
     * @return  string
     */
    public function renderUrl()
    {
        return $this->_urlBuilder->getUrl('launcher/redirect',  array('id' => $this->getId()));
    }

}
