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
 * Theme customization link model
 *
 * @method int getThemeId()
 * @method int getLayoutLinkId()
 * @method Mage_Core_Model_Theme_Customization_Link setThemeId()
 */
class Mage_Core_Model_Theme_Customization_Link extends Mage_Core_Model_Abstract
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Theme_Files
     */
    protected $_themeFiles;

    /**
     * Initialize dependencies
     *
     * @param Mage_Core_Model_Theme_Files $themeFiles
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_Cache $cacheManager
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Resource_Theme_Customization_Link $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Theme_Files $themeFiles,
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Resource_Theme_Customization_Link $resource,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($eventDispatcher, $cacheManager, $resource, $resourceCollection, $data);
        $this->_objectManager = $objectManager;
        $this->_themeFiles = $themeFiles;
    }

    /**
     * Get theme with customized files
     *
     * @return Mage_Core_Model_Theme
     * @throws Magento_Exception
     */
    public function getThemeId()
    {
        if (!$this->hasData('theme_id')) {
            throw new Magento_Exception('Theme id should be set');
        }
        return $this->getData('theme_id');
    }

    /**
     * Get layout link id for current theme customization files
     *
     * @return Mage_Core_Model_Layout_Link
     */
    protected function _getLinkByTheme()
    {
        if (!$this->getId()) {
            $this->load($this->getThemeId(), 'theme_id');
        }

        /** @var $link Mage_Core_Model_Layout_Update */
        $link = $this->_objectManager->create('Mage_Core_Model_Layout_Link');
        $linkId = $this->getLayoutLinkId();
        if ($linkId) {
            $link->load($linkId);
        }
        return $link;
    }

    /**
     * Get update model
     *
     * @param int $updateId
     * @return Mage_Core_Model_Layout_Update
     */
    protected function _getUpdate($updateId)
    {
        /** @var $update Mage_Core_Model_Layout_Update */
        $update = $this->_objectManager->create('Mage_Core_Model_Layout_Update');
        if ($updateId) {
            $update->load($updateId);
        }
        return $update;
    }

    /**
     * Get files collection for current theme
     *
     * @return Mage_Core_Model_Resource_Theme_Files_Collection
     */
    protected function _getFilesCollection()
    {
        $filesCollection = $this->_themeFiles->getCollection()
            ->setDefaultOrder(Varien_Data_Collection::SORT_ORDER_ASC)
            ->addFilter('theme_id', $this->getThemeId());
        return $filesCollection;
    }

    /**
     * Remove relation and layout update
     *
     * @return Mage_Core_Model_Theme_Customization_Link
     */
    public function _beforeDelete()
    {
        $link = $this->_getLinkByTheme();
        $update = $this->_getUpdate($link->getLayoutUpdateId());
        $link->delete();
        $update->delete();
        return parent::_beforeDelete();
    }

    /**
     * Add custom files to inclusion on frontend page
     *
     * @param string $handle
     * @return Mage_Core_Model_Theme_Customization_Link
     */
    public function changeCustomFilesUpdate($handle = 'default')
    {
        $link = $this->_getLinkByTheme();
        $customFiles = $this->_getFilesCollection()->getItems();
        if (empty($customFiles) && !$link->getId()) {
            return $this;
        } elseif (empty($customFiles) && $link->getId()) {
            $this->delete();
            return $this;
        }

        $update = $this->_getUpdate($link->getLayoutUpdateId());
        $this->_prepareUpdate($update, $customFiles);
        $update->setHandle($handle)->save();

        if (!$link->getId()) {
            $link->setThemeId($this->getThemeId())
                ->setLayoutUpdateId($update->getId())
                ->save();
            $this->setLayoutLinkId($link->getId())->save();
        }
        return $this;
    }

    /**
     * Add layout update for custom files
     *
     * @param Mage_Core_Model_Layout_Update $update
     * @param array $customFiles
     * @return Mage_Core_Model_Theme_Customization_Link
     */
    public function _prepareUpdate(Mage_Core_Model_Layout_Update $update, array $customFiles)
    {
        $xmlActions = '';
        /** @var $customFile Mage_Core_Model_Theme_Files */
        foreach ($customFiles as $customFile) {
            if ($customFile->hasContent()) {
                $xmlActions .= $this->_getInclusionAction($customFile);
            }
        }
        if (!empty($xmlActions)) {
            $update->setXml('<reference name="head">' . $xmlActions . '</reference>')->save();
        }
        return $this;
    }

    /**
     * Generate piece of layout update
     *
     * @param Mage_Core_Model_Theme_Files $customFile
     * @throws Magento_Exception
     * @return string
     */
    public function _getInclusionAction(Mage_Core_Model_Theme_Files $customFile)
    {
        switch ($customFile->getFileType()) {
            case Mage_Core_Model_Theme_Files::TYPE_CSS:
                $action =  "<action method=\"addCss\"><file>{$customFile->getRelativePath()}</file></action>";
                break;
            case Mage_Core_Model_Theme_Files::TYPE_JS:
                $action =  "<action method=\"addJs\"><file>{$customFile->getRelativePath()}</file></action>";
                break;
            default:
                throw new Magento_Exception('Unsupported file type format');
                break;
        }
        return $action;
    }

    /**
     * Clean cache
     *
     * @return Mage_Core_Model_Theme_Customization_Link
     */
    public function cleanCache()
    {
        Mage::app()->getCacheInstance()->invalidateType('layout');
        Mage::getDesign()->cleanMergedJsCss();
        return $this;
    }
}
