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
 * Theme Observer model
 */
class Mage_Core_Model_Theme_Observer
{
    /**
     * @var Mage_Core_Model_Theme_ImageFactory
     */
    protected $_themeImageFactory;

    /**
     * @var Mage_Core_Model_Resource_Layout_Update_Collection
     */
    protected $_updateCollection;

    /**
     * @param Mage_Core_Model_Theme_ImageFactory $themeImageFactory
     * @param Mage_Core_Model_Resource_Layout_Update_Collection $updateCollection
     */
    function __construct(
        Mage_Core_Model_Theme_ImageFactory $themeImageFactory,
        Mage_Core_Model_Resource_Layout_Update_Collection $updateCollection
    ) {
        $this->_themeImageFactory = $themeImageFactory;
        $this->_updateCollection = $updateCollection;
    }

    /**
     * Clean related contents to a theme
     *
     * @param Varien_Event_Observer $observer
     */
    public function cleanThemeRelatedContent(Varien_Event_Observer $observer)
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $observer->getEvent()->getData('theme');
        if ($theme) {
            $this->_themeImageFactory->create(array('theme' => $theme))->removePreviewImage();
            $this->_updateCollection->addThemeFilter($theme->getId())->delete();
        }
    }
}
