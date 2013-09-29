<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Website attribute backend
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Customer_Attribute_Backend_Website
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($logger, $data);
    }

    /**
     * Before save
     *
     * @param Magento_Object $object
     * @return Magento_Customer_Model_Customer_Attribute_Backend_Website
     */
    public function beforeSave($object)
    {
        if ($object->getId()) {
            return $this;
        }

        if (!$object->hasData('website_id')) {
            $object->setData('website_id', $this->_storeManager->getStore()->getWebsiteId());
        }

        return $this;
    }
}
