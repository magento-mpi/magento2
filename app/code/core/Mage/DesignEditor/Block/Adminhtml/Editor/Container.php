<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Editor toolbar
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Container extends Mage_Backend_Block_Widget_Container
{
    /**
     * Frame Url
     *
     * @var string
     */
    protected $_frameUrl;

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var $helper Mage_DesignEditor_Helper_Data */
        $helper = $this->_helperFactory->get('Mage_DesignEditor_Helper_Data');
        return $helper->__('Visual Design Editor');
    }

    /**
     * @param string $url
     *
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Container
     */
    public function setFrameUrl($url)
    {
        $this->_frameUrl = $url;
        return $this;
    }

    /**
     * Retrieve frame url
     *
     * @return string
     */
    public function getFrameUrl()
    {
        return $this->_frameUrl;
    }

//    /**
//     * Get configuration options for Visual Design Editor as JSON
//     *
//     * @return string
//     */
//    public function getOptionsJson()
//    {
//        $options = array(
//            'cookieHighlightingName' => Mage_DesignEditor_Model_Session::COOKIE_HIGHLIGHTING,
//        );
//        /** @var $toolbarRowBlock Mage_DesignEditor_Block_Template */
//        $toolbarRowBlock = $this->getChildBlock('design_editor_toolbar_row');
//
//        if ($toolbarRowBlock) {
//            /** @var $buttonsBlock Mage_DesignEditor_Block_Toolbar_Buttons */
//            $buttonsBlock = $toolbarRowBlock->getChildBlock('design_editor_toolbar_buttons');
//            if ($buttonsBlock) {
//                $options['compactLogUrl'] = $buttonsBlock->getCompactLogUrl();
//                $options['viewLayoutUrl'] = $buttonsBlock->getViewLayoutUrl();
//                $options['baseUrl'] = Mage::getBaseUrl();
//            }
//        }
//
//        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($options);
//    }
}
