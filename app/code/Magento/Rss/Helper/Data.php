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
namespace Magento\Rss\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Catalog product flat
     *
     * @var Magento_Catalog_Helper_Product_Flat
     */
    protected $_catalogProductFlat = null;

    /**
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Core_Helper_Context $context
    ) {
        $this->_catalogProductFlat = $catalogProductFlat;
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
            /* @var $emulationModel Magento\Core\Model\App\Emulation */
            $emulationModel = \Mage::getModel('Magento\Core\Model\App\Emulation');
            // Emulate admin environment to disable using flat model - otherwise we won't get global stats
            // for all stores
            $emulationModel->startEnvironmentEmulation(0, \Magento\Core\Model\App\Area::AREA_ADMIN);
        }
    }
}
