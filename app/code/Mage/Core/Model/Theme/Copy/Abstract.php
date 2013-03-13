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
 * Interface for copy theme
 */
abstract class Mage_Core_Model_Theme_Copy_Abstract implements Mage_Core_Model_Theme_Copy_Interface
{
    /**
     * Theme model factory
     *
     * @var Mage_Core_Model_Theme_Factory
     */
    protected $_themeFactory;

    /**
     * @var Mage_Core_Model_Resource_Layout_Link_Collection
     */
    protected $_linkCollection;

    /**
     * @param Mage_Core_Model_Theme_Factory $themeFactory
     * @param Mage_Core_Model_Resource_Layout_Link_Collection $linkCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Theme_Factory $themeFactory,
        Mage_Core_Model_Resource_Layout_Link_Collection $linkCollection,
        array $data = array()
    ) {
        $this->_themeFactory = $themeFactory;
        $this->_linkCollection = $linkCollection;
    }

    /**
     * Copy theme customizations
     *
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Theme $stagingTheme
     */
    protected function _copyLayoutUpdates($theme, $stagingTheme)
    {
        /** @var $collection Mage_Core_Model_Resource_Layout_Link_Collection */
        $collection = $this->_linkCollection->addTemporaryFilter(false)
            ->addFieldToFilter('theme_id', $theme->getId());

        /** @var $link Mage_Core_Model_Layout_Link */
        foreach ($collection as $link) {
            //copy links from 'virtual' to 'staging' theme
            $link->setId(null);
            $link->setThemeId($stagingTheme->getId());
            $link->save();
        }
    }
}
