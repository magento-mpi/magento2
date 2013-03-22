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
 * @method int getLayoutUpdateId()
 * @method Mage_Core_Model_Theme_Customization_Update setLayoutUpdateId()
 * @method Mage_Core_Model_Theme_Customization_Update setThemeId()
 */
class Mage_Core_Model_Theme_Customization_Update extends Mage_Core_Model_Abstract
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Theme_File
     */
    protected $_themeFiles;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_designPackage;

    /**
     * @param Mage_Core_Model_Context $context
     * @param Mage_Core_Model_Theme_File $themeFiles
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Resource_Theme_Customization_Update $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Mage_Core_Model_Theme_File $themeFiles,
        Mage_Core_Model_Design_Package $designPackage,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Resource_Theme_Customization_Update $resource,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_objectManager = $objectManager;
        $this->_themeFiles = $themeFiles;
        $this->_designPackage = $designPackage;
    }

    /**
     * Get theme id
     *
     * @return int
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
     * Add custom files to inclusion on frontend page
     *
     * @param string $handle
     * @return Mage_Core_Model_Theme_Customization_Update
     */
    public function updateCustomFilesUpdate($handle = 'default')
    {
        if (!$this->getId()) {
            $this->load($this->getThemeId(), 'theme_id');
        }

        $update = $this->_getUpdate();
        $link = $this->_getLink();

        $customFiles = $this->_getFilesCollection()->getItems();
        if (empty($customFiles)) {
            if ($update->getId()) {
                $update->delete();
            }
            return $this;
        }

        $this->_prepareUpdate($update, $customFiles);
        $update->setHandle($handle)
            ->save();

        if (!$link->getId()) {
            $link->setThemeId($this->getThemeId())
                ->setLayoutUpdateId($update->getId())
                ->setStoreId(Mage_Core_Model_AppInterface::ADMIN_STORE_ID)  //NOTE actually it means 'All stores'
                ->setIsTemporary(false)
                ->save();
        }
        if (!$this->getId()) {
            $this->setLayoutUpdateId($update->getId())->save();
        }
        return $this;
    }

    /**
     * Get layout update that adds customization files for current theme
     *
     * @return Mage_Core_Model_Layout_Link
     */
    protected function _getUpdate()
    {
        /** @var $update Mage_Core_Model_Layout_Update */
        $update = $this->_objectManager->create('Mage_Core_Model_Layout_Update');
        $updateId = $this->getLayoutUpdateId();
        if ($updateId) {
            $update->load($updateId);
        }
        return $update;
    }

    /**
     * Get 'layout link' that links theme and layout update that adds customization files to that theme
     *
     * @return Mage_Core_Model_Layout_Link
     */
    protected function _getLink()
    {
        /** @var $collection Mage_Core_Model_Resource_Layout_Link_Collection */
        $collection = $this->_objectManager->create('Mage_Core_Model_Resource_Layout_Link_Collection');

        /** @var $link Mage_Core_Model_Layout_Update */
        $link = $collection->addFieldToFilter('theme_id', $this->getThemeId())
            ->addFieldToFilter('layout_update_id', $this->getLayoutUpdateId())
            ->getFirstItem();

        return $link;
    }

    /**
     * Get files collection for current theme
     *
     * @return Mage_Core_Model_Resource_Theme_File_Collection
     */
    protected function _getFilesCollection()
    {
        //NOTE: Added filter is_temporary=0 cause now  there is a hidden way to include JS file which is still temporary
        $filesCollection = $this->_themeFiles->getCollection()
            ->setDefaultOrder(Varien_Data_Collection::SORT_ORDER_ASC)
            ->addFilter('theme_id', $this->getThemeId())
            ->addFilter('is_temporary', false);
        return $filesCollection;
    }

    /**
     * Add layout update for custom files
     *
     * @param Mage_Core_Model_Layout_Update $update
     * @param array $customFiles
     * @return Mage_Core_Model_Theme_Customization_Update
     */
    protected function _prepareUpdate(Mage_Core_Model_Layout_Update $update, array $customFiles)
    {
        $xmlActions = array();
        /** @var $customFile Mage_Core_Model_Theme_File */
        foreach ($customFiles as $customFile) {
            if ($customFile->hasContent()) {
                $xmlActions[] = $this->_getInclusionAction($customFile);
            }
        }
        if (!empty($xmlActions)) {
            $update->setXml('<reference name="head">' . join('', $xmlActions) . '</reference>')->save();
        }
        $params = array(
            'area'       => Mage_Core_Model_Design_Package::DEFAULT_AREA,
            'themeModel' => $customFile->getTheme()
        );
        $this->_designPackage->dropPublicationCache($params);
        return $this;
    }

    /**
     * Generate piece of layout update
     *
     * @param Mage_Core_Model_Theme_File $customFile
     * @throws Magento_Exception
     * @return string
     */
    protected function _getInclusionAction(Mage_Core_Model_Theme_File $customFile)
    {
        switch ($customFile->getFileType()) {
            case Mage_Core_Model_Theme_File::TYPE_CSS:
                $action =  sprintf('<action method="addCss"><file>%s</file></action>', $customFile->getRelativePath());
                break;
            case Mage_Core_Model_Theme_File::TYPE_JS:
                $action =  sprintf('<action method="addJs"><file>%s</file></action>', $customFile->getRelativePath());
                break;
            default:
                throw new Magento_Exception(sprintf('Unsupported file type format "%s"', $customFile->getFileType()));
                break;
        }
        return $action;
    }
}
