<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme factory
 */
class Mage_Core_Model_Theme_Factory
{
    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Model_Theme_CopyService
     */
    protected $_themeCopyService;

    /**
     * Object Manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Helper_Data $helper
     * @param Mage_Core_Model_Theme_CopyService $themeCopyService
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Helper_Data $helper,
        Mage_Core_Model_Theme_CopyService $themeCopyService
    ) {
        $this->_objectManager = $objectManager;
        $this->_helper = $helper;
        $this->_themeCopyService = $themeCopyService;
    }

    /**
     * Create new instance
     *
     * @param array $data
     * @return Mage_Core_Model_Theme
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Mage_Core_Model_Theme', $data);
    }

    /**
     * Create theme customization
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Theme
     */
    public function createThemeCustomization($theme)
    {
        $themeCopyCount = $this->_getThemeCustomizations()->addFilter('parent_id', $theme->getId())->count();

        $themeData = $theme->getData();
        $themeData['parent_id'] = $theme->getId();
        $themeData['theme_id'] = null;
        $themeData['theme_path'] = null;
        $themeData['theme_title'] = sprintf(
            "%s - %s #%s",
            $theme->getThemeTitle(),
            $this->_helper->__('Copy'),
            ($themeCopyCount + 1)
        );
        $themeData['type'] = Mage_Core_Model_Theme::TYPE_VIRTUAL;

        /** @var $themeCustomization Mage_Core_Model_Theme */
        $themeCustomization = $this->create()->setData($themeData);
        $themeCustomization->getThemeImage()->createPreviewImageCopy();
        $themeCustomization->save();

        $this->_themeCopyService->copy($theme, $themeCustomization);

        return $themeCustomization;
    }

    /**
     * Return theme customizations collection
     *
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    protected function _getThemeCustomizations()
    {
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->create()->getCollection();
        $collection->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)
            ->addTypeFilter(Mage_Core_Model_Theme::TYPE_VIRTUAL);
        return $collection;
    }
}
