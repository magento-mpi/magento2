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
        /** @var $sourceCollection Mage_Core_Model_Resource_Layout_Link_Collection */
        $sourceCollection = $this->_layoutLink->getCollection();
        $sourceCollection->addTemporaryFilter(false)->addFieldToFilter('theme_id', $sourceTheme->getId());

        /** @var $targetCollection Mage_Core_Model_Resource_Layout_Link_Collection */
        $targetCollection = $this->_layoutLink->getCollection();
        $targetCollection->addTemporaryFilter(false)->addFieldToFilter('theme_id', $targetTheme->getId());

        /** @var $link Mage_Core_Model_Layout_Link */
        foreach ($sourceCollection as $link) {
            if (!$this->_isLinkCreated($targetCollection, $targetTheme->getId(), $link->getLayoutUpdateId())) {
                $link->setId(null);
                $link->setThemeId($targetTheme->getId());
                $link->save();
            }
        }
        return $this;
    }

    /**
     * Check if link is already created
     *
     * @param Mage_Core_Model_Resource_Layout_Link_Collection $targetCollection
     * @param int $updateId
     * @param int $themeId
     * @return bool
     */
    protected function _isLinkCreated($targetCollection, $themeId, $updateId)
    {
        /** @var $createdLink Mage_Core_Model_Layout_Link */
        foreach ($targetCollection as $createdLink) {
            if ($createdLink->getThemeId() == $themeId && $createdLink->getLayoutUpdateId() == $updateId) {
                return true;
            }
        }
        return false;
    }
}
