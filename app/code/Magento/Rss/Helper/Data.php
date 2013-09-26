<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rss data helper
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Catalog product flat
     *
     * @var Magento_Catalog_Helper_Product_Flat
     */
    protected $_catalogProductFlat;

    /**
     * @var Magento_Core_Model_App_EmulationFactory
     */
    protected $_emulationFactory;

    /**
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_App_EmulationFactory $emulationFactory
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_App_EmulationFactory $emulationFactory
    ) {
        $this->_catalogProductFlat = $catalogProductFlat;
        $this->_emulationFactory = $emulationFactory;
        parent::__construct($context);
    }

    /**
     * Disable using of flat catalog and/or product model to prevent limiting results to single store. Probably won't
     * work inside a controller.
     *
     * @return null
     */
    public function disableFlat()
    {
        if ($this->_catalogProductFlat->isAvailable()) {
            /* @var $emulationModel Magento_Core_Model_App_Emulation */
            $emulationModel = $this->_emulationFactory->create();
            // Emulate admin environment to disable using flat model - otherwise we won't get global stats
            // for all stores
            $emulationModel->startEnvironmentEmulation(0, Magento_Core_Model_App_Area::AREA_ADMIN);
        }
    }
}
