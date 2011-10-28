<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   Copyright (c) 2009 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $locale = Mage::getModel('moneybookers/acc')->getLocale();
        $file = "Phoenix_Moneybookers::images/banner_120_{$locale}.png";
        if (file_exists(Mage::getDesign()->getSkinFile($file))) {
            return Mage::getDesign()->getSkinUrl($file);
        }

        return $this->getSkinUrl('Phoenix_Moneybookers::images/banner_120_int.gif');
    }
}
