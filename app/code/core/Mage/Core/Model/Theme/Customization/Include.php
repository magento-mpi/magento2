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
 * Theme customization files including to front page
 */
class Mage_Core_Model_Theme_Customization_Include
{
    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Customizations type
     *
     * @var array
     */
    protected $_customizationsType = array(
        'Mage_Core_Model_Theme_Customization_Files_Css' => 'addCss'
    );

    /**
     * Initialize service model
     *
     * @param Mage_Core_Model_Design_Package $design
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Mage_Core_Model_Design_Package $design,
        Magento_ObjectManager $objectManager
    ) {
        $this->_design        = $design;
        $this->_objectManager = $objectManager;
    }

    /**
     * Add theme customization
     *
     * @param Mage_Core_Model_Layout $layout
     * @return Mage_Core_Model_Theme_Service
     */
    public function addThemeCustomization($layout)
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_design->getDesignTheme();

        /** @var $blockHead Mage_Page_Block_Html_Head */
        $blockHead = $layout->getBlock('head');

        foreach ($this->_customizationsType as $type => $action) {
            /** @var $customisation Mage_Core_Model_Theme_Customization_CustomizationInterface */
            $customisation = $this->_objectManager->create($type);

            /** @var $themeCustomizations Mage_Core_Model_Resource_Theme_Files_Collection */
            $themeCustomizations = $theme->setCustomization($customisation)->getCustomizationData($type::TYPE);

            /** @var $themeCustomization Mage_Core_Model_Theme_Files */
            foreach ($themeCustomizations as $themeCustomization) {
                $blockHead->{$action}($themeCustomization->getFilePath());
            }
        }

        return $this;
    }
}
