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
 * Link Tracker model
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_LinkTracker extends Mage_Core_Model_Abstract
{
    /**
     * Url Builder
     *
     * @var Mage_Core_Model_UrlInterface
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
     * @param Mage_Core_Model_UrlInterface $urlBuilder
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Mage_Core_Model_UrlInterface $urlBuilder,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_urlBuilder = $urlBuilder;
        $this->_init('Saas_Launcher_Model_Resource_LinkTracker');
    }

    /**
     * Render URL, which points to launcher redirect controller
     *
     * @return  string
     */
    public function renderUrl()
    {
        return $this->_urlBuilder->getUrl('launcher/redirect', array('id' => $this->getId()));
    }

}
