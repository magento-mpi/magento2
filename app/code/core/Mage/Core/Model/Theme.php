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
 * Theme model class
 */
class Mage_Core_Model_Theme extends Mage_Core_Model_Abstract
{
    /**
     * Theme model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Theme');
    }

    /**
     * Load package data
     *
     * @return Mage_Core_Model_Theme
     */
    public function loadPackageData()
    {
        $this->getResource()->loadPackageData($this);
        return $this;
    }

    /**
     * Processing theme after loading data
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _afterLoad()
    {
        $this->loadPackageData();
        return parent::_afterLoad();
    }

    /**
     * Get skin list
     *
     * @return array
     */
    public function getSkinList()
    {
        $result = array();
        $skinPaths = glob($this->_getSkinFolderPattern(), GLOB_ONLYDIR);

        foreach ($skinPaths as $skinPath) {
            $skinPath = str_replace(DS, '/', $skinPath);
            if (preg_match('/\/(?P<skin>[^\/.]+)$/i', $skinPath, $skinMatches)) {
                $result[$skinMatches['skin']] = implode('/', array($this->getPackageCode(), $this->getThemeCode(),
                                                                   $skinMatches['skin']));
            }
        }
        return $result;
    }

    /**
     * Get skin folder pattern
     *
     * @return string
     */
    protected function _getSkinFolderPattern()
    {
        return implode(
            DS, array(Mage::getBaseDir('design'), Mage_Core_Model_Design_Package::DEFAULT_AREA,
                      $this->getPackageCode(), $this->getThemeCode(), 'skin', '*')
        );
    }

    /**
     * Get themes preview image url
     *
     * @return string
     */
    public function getPreviewImageUrl()
    {
        /** @todo Temporary solution for placeholder image */
        /** @var $imageHelper Mage_XmlConnect_Helper_Image */
        $imageHelper = Mage::helper('Mage_XmlConnect_Helper_Image');
        return $imageHelper->getSkinImagesUrl('mobile_preview/ipad/product_image.jpg');
    }
}
