<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Emulation model
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\App;

class Emulation extends \Magento\Object
{
    /**
     * Design package instance
     *
     * @var \Magento\Core\Model\View\DesignInterface
     */
    protected $_design = null;

    /**
     * @param \Magento\Core\Model\View\DesignInterface $design
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\View\DesignInterface $design,
        array $data = array()
    ) {
        $this->_design = $design;
        parent::__construct($data);
    }

    /**
     * Start environment emulation of the specified store
     *
     * Function returns information about initial store environment and emulates environment of another store
     *
     * @param integer $storeId
     * @param string $area
     * @param bool $emulateStoreInlineTranslation emulate inline translation of the specified store or just disable it
     *
     * @return \Magento\Object information about environment of the initial store
     */
    public function startEnvironmentEmulation($storeId, $area = \Magento\Core\Model\App\Area::AREA_FRONTEND,
        $emulateStoreInlineTranslation = false
    ) {
        if ($area === null) {
            $area = \Magento\Core\Model\App\Area::AREA_FRONTEND;
        }
        $initialTranslateInline = $emulateStoreInlineTranslation
            ? $this->_emulateInlineTranslation($storeId, $area)
            : $this->_emulateInlineTranslation();
        $initialDesign = $this->_emulateDesign($storeId, $area);
        // Current store needs to be changed right before locale change and after design change
        \Mage::getObjectManager()->get('Magento\Core\Model\StoreManager')->setCurrentStore($storeId);
        $initialLocaleCode = $this->_emulateLocale($storeId, $area);

        $initialEnvironmentInfo = new \Magento\Object();
        $initialEnvironmentInfo->setInitialTranslateInline($initialTranslateInline)
            ->setInitialDesign($initialDesign)
            ->setInitialLocaleCode($initialLocaleCode);

        return $initialEnvironmentInfo;
    }

    /**
     * Stop enviromment emulation
     *
     * Function restores initial store environment
     *
     * @param \Magento\Object $initialEnvironmentInfo information about environment of the initial store
     *
     * @return \Magento\Core\Model\App\Emulation
     */
    public function stopEnvironmentEmulation(\Magento\Object $initialEnvironmentInfo)
    {
        $this->_restoreInitialInlineTranslation($initialEnvironmentInfo->getInitialTranslateInline());
        $initialDesign = $initialEnvironmentInfo->getInitialDesign();
        $this->_restoreInitialDesign($initialDesign);
        // Current store needs to be changed right before locale change and after design change
        \Mage::getObjectManager()->get('Magento\Core\Model\StoreManager')->setCurrentStore($initialDesign['store']);
        $this->_restoreInitialLocale($initialEnvironmentInfo->getInitialLocaleCode(), $initialDesign['area']);
        return $this;
    }

    /**
     * Emulate inline translation of the specified store
     *
     * Function disables inline translation if $storeId is null
     *
     * @param integer|null $storeId
     * @param string $area
     *
     * @return boolean initial inline translation state
     */
    protected function _emulateInlineTranslation($storeId = null, $area = \Magento\Core\Model\App\Area::AREA_FRONTEND)
    {
        if (is_null($storeId)) {
            $newTranslateInline = false;
        } else {
            if ($area == \Magento\Core\Model\App\Area::AREA_ADMIN) {
                $newTranslateInline = \Mage::getStoreConfigFlag('dev/translate_inline/active_admin', $storeId);
            } else {
                $newTranslateInline = \Mage::getStoreConfigFlag('dev/translate_inline/active', $storeId);
            }
        }
        /** @var $translateModel \Magento\Core\Model\Translate */
        $translateModel = \Mage::getObjectManager()->get('Magento\Core\Model\Translate');
        $initialTranslateInline = $translateModel->getTranslateInline();
        $translateModel->setTranslateInline($newTranslateInline);
        return $initialTranslateInline;
    }

    /**
     * Apply design of the specified store
     *
     * @param integer $storeId
     * @param string $area
     *
     * @return array initial design parameters(package, store, area)
     */
    protected function _emulateDesign($storeId, $area = \Magento\Core\Model\App\Area::AREA_FRONTEND)
    {
        /** @var $objectManager \Magento\ObjectManager */
        $objectManager = \Mage::getObjectManager();

        /** @var $store \Magento\Core\Model\StoreManager */
        $store = $objectManager->get('Magento\Core\Model\StoreManager')->getStore();

        $initialDesign = array(
            'area' => $this->_design->getArea(),
            'theme' => $this->_design->getDesignTheme(),
            'store' => $store
        );

        $storeTheme = $this->_design->getConfigurationDesignTheme($area, array('store' => $storeId));
        $this->_design->setDesignTheme($storeTheme, $area);

        if ($area == \Magento\Core\Model\App\Area::AREA_FRONTEND) {
            $designChange = $objectManager->get('Magento\Core\Model\Design')->loadChange($storeId);
            if ($designChange->getData()) {
                $this->_design->setDesignTheme($designChange->getDesign(), $area);
            }
        }

        return $initialDesign;
    }

    /**
     * Apply locale of the specified store
     *
     * @param integer $storeId
     * @param string $area
     *
     * @return string initial locale code
     */
    protected function _emulateLocale($storeId, $area = \Magento\Core\Model\App\Area::AREA_FRONTEND)
    {
        $initialLocaleCode = \Mage::app()->getLocale()->getLocaleCode();
        $newLocaleCode = \Mage::getStoreConfig(\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_LOCALE, $storeId);
        \Mage::app()->getLocale()->setLocaleCode($newLocaleCode);
        \Mage::getObjectManager()->get('Magento\Core\Helper\Translate')
            ->initTranslate($newLocaleCode, $area, true);
        return $initialLocaleCode;
    }

    /**
     * Restore initial inline translation state
     *
     * @param boolean $initialTranslateInline
     *
     * @return \Magento\Core\Model\App\Emulation
     */
    protected function _restoreInitialInlineTranslation($initialTranslateInline)
    {
        $translateModel = \Mage::getObjectManager()->get('Magento\Core\Model\Translate');
        $translateModel->setTranslateInline($initialTranslateInline);
        return $this;
    }

    /**
     * Restore design of the initial store
     *
     * @param array $initialDesign
     *
     * @return \Magento\Core\Model\App\Emulation
     */
    protected function _restoreInitialDesign(array $initialDesign)
    {
        $this->_design->setDesignTheme($initialDesign['theme'], $initialDesign['area']);
        return $this;
    }

    /**
     * Restore locale of the initial store
     *
     * @param string $initialLocaleCode
     * @param string $initialArea
     *
     * @return \Magento\Core\Model\App\Emulation
     */
    protected function _restoreInitialLocale($initialLocaleCode, $initialArea = \Magento\Core\Model\App\Area::AREA_ADMIN)
    {
        /** @var $app \Magento\Core\Model\App */
        $app = \Mage::getObjectManager()->get('Magento\Core\Model\App');

        $app->getLocale()->setLocaleCode($initialLocaleCode);
        \Mage::getObjectManager()->get('Magento\Core\Helper\Translate')
            ->initTranslate($initialLocaleCode, $initialArea, true);
        return $this;
    }
}
