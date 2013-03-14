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
     * @var Mage_Core_Model_Layout_Link
     */
    protected $_layoutLink;

    /**
     * @var Mage_Core_Model_Layout_Update
     */
    protected $_layoutUpdate;

    /**
     * @param Mage_Core_Model_Theme_Factory $themeFactory
     * @param Mage_Core_Model_Layout_Link $layoutLink
     * @param Mage_Core_Model_Layout_Update $layoutUpdate
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Theme_Factory $themeFactory,
        Mage_Core_Model_Layout_Link $layoutLink,
        Mage_Core_Model_Layout_Update $layoutUpdate,
        array $data = array()
    ) {
        $this->_themeFactory = $themeFactory;
        $this->_layoutLink = $layoutLink;
        $this->_layoutUpdate = $layoutUpdate;
    }

    /**
     * Copy theme customizations
     *
     * @param Mage_Core_Model_Theme $sourceTheme
     * @param Mage_Core_Model_Theme $targetTheme
     * @return Mage_Core_Model_Theme_Copy_Abstract
     */
    protected function _copyLayoutUpdates($sourceTheme, $targetTheme)
    {
        /** @var $collection Mage_Core_Model_Resource_Layout_Link_Collection */
        $collection = $this->_layoutLink->getCollection();
        $collection->addTemporaryFilter(false)->addFieldToFilter('theme_id', $sourceTheme->getId());

        /** @var $link Mage_Core_Model_Layout_Link */
        foreach ($collection as $link) {
            $link->setId(null);
            $link->setThemeId($targetTheme->getId());
            $link->save();
        }
        return $this;
    }

    /**
     * @param Mage_Core_Model_Theme $theme
     * @param int $layoutUpdateId
     * @return Mage_Core_Model_Resource_Layout_Link_Collection
     */
    protected function _loadLayoutLinks(Mage_Core_Model_Theme $theme, $layoutUpdateId)
    {
        /** @var $collection Mage_Core_Model_Resource_Layout_Link_Collection */
        $collection = $this->_layoutLink->getCollection();
        $collection->addFieldToFilter('theme_id', $theme->getId())
            ->addFieldToFilter('layout_update_id', $layoutUpdateId);
        return $collection;
    }
}
