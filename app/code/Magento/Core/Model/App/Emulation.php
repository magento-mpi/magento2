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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\TranslateInterface
     */
    protected $_translate;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var \Magento\Core\Model\Design
     */
    protected $_design;

    /**
     * @var \Magento\Translate\Inline\ConfigInterface
     */
    protected $inlineConfig;

    /**
     * @var \Magento\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\View\DesignInterface $viewDesign
     * @param \Magento\Core\Model\Design $design
     * @param \Magento\TranslateInterface $translate
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\View\DesignInterface $viewDesign,
        \Magento\Core\Model\Design $design,
        \Magento\TranslateInterface $translate,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Translate\Inline\ConfigInterface $inlineConfig,
        \Magento\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Locale\ResolverInterface $localeResolver,
        array $data = array()
    ) {
        $this->_localeResolver = $localeResolver;
        parent::__construct($data);
        $this->_storeManager = $storeManager;
        $this->_viewDesign = $viewDesign;
        $this->_design = $design;
        $this->_translate = $translate;
        $this->_storeConfig = $coreStoreConfig;
        $this->inlineConfig = $inlineConfig;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * Start environment emulation of the specified store
     *
     * Function returns information about initial store environment and emulates environment of another store
     *
     * @param integer $storeId
     * @param string $area
     * @param bool $emulateStoreInlineTranslation emulate inline translation of the specified store or just disable it
     * @return \Magento\Object information about environment of the initial store
     */
    public function startEnvironmentEmulation(
        $storeId,
        $area = \Magento\Core\Model\App\Area::AREA_FRONTEND,
        $emulateStoreInlineTranslation = false
    ) {
        if ($area === null) {
            $area = \Magento\Core\Model\App\Area::AREA_FRONTEND;
        }
        $initialTranslateInline = $emulateStoreInlineTranslation ? $this->_emulateInlineTranslation(
            $storeId
        ) : $this->_emulateInlineTranslation();
        $initialDesign = $this->_emulateDesign($storeId, $area);
        // Current store needs to be changed right before locale change and after design change
        $this->_storeManager->setCurrentStore($storeId);
        $initialLocaleCode = $this->_emulateLocale($storeId, $area);

        $initialEnvironmentInfo = new \Magento\Object();
        $initialEnvironmentInfo->setInitialTranslateInline(
            $initialTranslateInline
        )->setInitialDesign(
            $initialDesign
        )->setInitialLocaleCode(
            $initialLocaleCode
        );

        return $initialEnvironmentInfo;
    }

    /**
     * Stop environment emulation
     *
     * Function restores initial store environment
     *
     * @param \Magento\Object $initialEnvironmentInfo information about environment of the initial store
     * @return \Magento\Core\Model\App\Emulation
     */
    public function stopEnvironmentEmulation(\Magento\Object $initialEnvironmentInfo)
    {
        $this->_restoreInitialInlineTranslation($initialEnvironmentInfo->getInitialTranslateInline());
        $initialDesign = $initialEnvironmentInfo->getInitialDesign();
        $this->_restoreInitialDesign($initialDesign);
        // Current store needs to be changed right before locale change and after design change
        $this->_storeManager->setCurrentStore($initialDesign['store']);
        $this->_restoreInitialLocale($initialEnvironmentInfo->getInitialLocaleCode(), $initialDesign['area']);
        return $this;
    }

    /**
     * Emulate inline translation of the specified store
     *
     * Function disables inline translation if $storeId is null
     *
     * @param integer|null $storeId
     * @return boolean initial inline translation state
     */
    protected function _emulateInlineTranslation($storeId = null)
    {
        if (is_null($storeId)) {
            $newTranslateInline = false;
        } else {
            $newTranslateInline = $this->inlineConfig->isActive($storeId);
        }

        $translateInline = $this->inlineTranslation->isEnabled();
        $this->inlineTranslation->suspend($newTranslateInline);
        return $translateInline;
    }

    /**
     * Apply design of the specified store
     *
     * @param integer $storeId
     * @param string $area
     * @return array initial design parameters(package, store, area)
     */
    protected function _emulateDesign($storeId, $area = \Magento\Core\Model\App\Area::AREA_FRONTEND)
    {
        $store = $this->_storeManager->getStore();
        $initialDesign = array(
            'area' => $this->_viewDesign->getArea(),
            'theme' => $this->_viewDesign->getDesignTheme(),
            'store' => $store
        );

        $storeTheme = $this->_viewDesign->getConfigurationDesignTheme($area, array('store' => $storeId));
        $this->_viewDesign->setDesignTheme($storeTheme, $area);

        if ($area == \Magento\Core\Model\App\Area::AREA_FRONTEND) {
            $designChange = $this->_design->loadChange($storeId);
            if ($designChange->getData()) {
                $this->_viewDesign->setDesignTheme($designChange->getDesign(), $area);
            }
        }

        return $initialDesign;
    }

    /**
     * Apply locale of the specified store
     *
     * @param integer $storeId
     * @param string $area
     * @return string initial locale code
     */
    protected function _emulateLocale($storeId, $area = \Magento\Core\Model\App\Area::AREA_FRONTEND)
    {
        $initialLocaleCode = $this->_localeResolver->getLocaleCode();
        $newLocaleCode = $this->_storeConfig->getValue(
            $this->_localeResolver->getDefaultLocalePath(),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $this->_localeResolver->setLocaleCode($newLocaleCode);
        $this->_translate->setLocale($newLocaleCode)->loadData($area, true);

        return $initialLocaleCode;
    }

    /**
     * Restore initial inline translation state
     *
     * @param bool $initialTranslate
     * @return $this
     */
    protected function _restoreInitialInlineTranslation($initialTranslate)
    {
        $this->inlineTranslation->resume($initialTranslate);
        return $this;
    }

    /**
     * Restore design of the initial store
     *
     * @param array $initialDesign
     * @return $this
     */
    protected function _restoreInitialDesign(array $initialDesign)
    {
        $this->_viewDesign->setDesignTheme($initialDesign['theme'], $initialDesign['area']);
        return $this;
    }

    /**
     * Restore locale of the initial store
     *
     * @param string $initialLocaleCode
     * @param string $initialArea
     * @return $this
     */
    protected function _restoreInitialLocale(
        $initialLocaleCode,
        $initialArea = \Magento\Core\Model\App\Area::AREA_ADMIN
    ) {
        $this->_localeResolver->setLocaleCode($initialLocaleCode);
        $this->_translate->setLocale($initialLocaleCode)->loadData($initialArea, true);

        return $this;
    }
}
