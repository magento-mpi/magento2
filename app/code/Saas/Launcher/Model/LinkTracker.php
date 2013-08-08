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
class Saas_Launcher_Model_LinkTracker extends Magento_Core_Model_Abstract
{
    /**
     * Url Builder
     *
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Prefix for model event names
     *
     * @var string
     */
    protected $_eventPrefix = 'launcher_link_tracker';

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_Core_Model_Resource_Abstract $resource = null,
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
