<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog session model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * Class constructor. Initialize session namespace
     *
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param string $sessionName
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        $sessionName = null,
        array $data = array()
    ) {
        parent::__construct($coreStoreConfig, $data);
        $this->init('catalog', $sessionName);
    }

    public function getDisplayMode()
    {
        return $this->_getData('display_mode');
    }
}
