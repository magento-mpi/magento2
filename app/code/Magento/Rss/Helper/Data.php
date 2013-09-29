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
     * @var \Magento\Catalog\Helper\Product\Flat
     */
    protected $_catalogProductFlat;

    /**
     * @var \Magento\Core\Model\App\EmulationFactory
     */
    protected $_emulationFactory;

    /**
     * @param \Magento\Catalog\Helper\Product\Flat $catalogProductFlat
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\App\EmulationFactory $emulationFactory
     */
    public function __construct(
        \Magento\Catalog\Helper\Product\Flat $catalogProductFlat,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\App\EmulationFactory $emulationFactory
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
            /* @var $emulationModel \Magento\Core\Model\App\Emulation */
            $emulationModel = $this->_emulationFactory->create();
            // Emulate admin environment to disable using flat model - otherwise we won't get global stats
            // for all stores
            $emulationModel->startEnvironmentEmulation(0, \Magento\Core\Model\App\Area::AREA_ADMIN);
        }
    }
}
