<?php
/**
 * {license_notice}
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Phoenix_Moneybookers_Block_Payment extends Mage_Core_Block_Template
{
    /**
     * Return Payment logo src
     *
     * @return string
     */
    public function getMoneybookersLogoSrc()
    {
        $locale = Mage::getModel('Phoenix_Moneybookers_Model_Acc')->getLocale();
        $file = "Phoenix_Moneybookers::images/banner_120_{$locale}.png";
        if (file_exists(Mage::getDesign()->getSkinFile($file))) {
            return Mage::getDesign()->getSkinUrl($file);
        }

        return $this->getSkinUrl('Phoenix_Moneybookers::images/banner_120_int.gif');
    }
}
