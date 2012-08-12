<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Run extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('system/convert/profile/run.phtml');
    }

    public function getRunButtonHtml()
    {
        $html = '';
/*
        if (Mage::registry('current_convert_profile')->getDirection()=='import') {
            $html .= $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setType('button')
                ->setLabel($this->__('Upload import file'))
                ->setOnClick('showUpload()')
                ->toHtml();
        }
*/
        /*
        $html .= $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setType('button')
            ->setClass('save')->setLabel($this->__('Run Profile Inside This Window'))
            ->setOnClick('runProfile()')
            ->toHtml();
        */

        $html .= $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setType('button')
            ->setClass('save')->setLabel($this->__('Run Profile in Popup'))
            ->setOnClick('runProfile(true)')
            ->toHtml();

        return $html;
    }

    public function getProfileId()
    {
        return Mage::registry('current_convert_profile')->getId();
    }

    public function getImportedFiles()
    {
        $files = array();
        $path = Mage::app()->getConfig()->getTempVarDir().'/import';
        if (!is_readable($path)) {
            return $files;
        }
        $dir = dir($path);
        while (false !== ($entry = $dir->read())) {
            if($entry != '.'
               && $entry != '..'
               && in_array(strtolower(substr($entry, strrpos($entry, '.')+1)), array($this->getParseType())))
            {
                $files[] = $entry;
            }
        }
        sort($files);
        $dir->close();
        return $files;
    }

    public function getParseType()
    {
        $data = Mage::registry('current_convert_profile')->getGuiData();
        if ($data)
            return ($data['parse']['type'] == 'excel_xml') ? 'xml': $data['parse']['type'];
    }
}
