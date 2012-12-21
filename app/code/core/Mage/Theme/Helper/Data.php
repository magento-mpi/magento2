<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme data helper
 */
class Mage_Theme_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get CSS files of a given theme
     *
     * Returned array has a structure
     * array('Mage_Catalog::widgets.css' => 'http://mage2.com/pub/media/theme/frontend/_theme15/en_US/Mage_Cms/widgets.css')
     *
     * @param Mage_Core_Model_Theme $theme
     * @return array
     */
    public function getCssFiles($theme)
    {
        $arguments = array(
            'area'  => $theme->getArea(),
            'theme' => $theme->getId()
        );
        /** @var $layoutMerge Mage_Core_Model_Layout_Merge */
        $layoutMerge = Mage::getModel('Mage_Core_Model_Layout_Merge', array('arguments' => $arguments));
        $layoutElement = $layoutMerge->getFileLayoutUpdatesXml();
        
        $xpathRefs = '//reference[@name="head"]/action[@method="addCss" or @method="addCssIe"]/*[1]';
        $xpathBlocks = '//block[@type="Mage_Page_Block_Html_Head"]/action[@method="addCss" or @method="addCssIe"]/*[1]';
        $files = array_merge(
            $layoutElement->xpath($xpathRefs),
            $layoutElement->xpath($xpathBlocks)
        );

        $design = Mage::getDesign();
        $params = array(
            'area'       => $theme->getArea(),
            'themeModel' => $theme
        );
        $urls = array();
        foreach ($files as $file) {
            $urls[(string)$file] = array(
                'filename' => $design->getViewFile($file, $params),
                'url'      => $design->getViewFileUrl($file, $params)
            );
        }

        return $urls;
    }
}
