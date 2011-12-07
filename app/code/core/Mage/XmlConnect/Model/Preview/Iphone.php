<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Iphone preview model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Preview_Iphone extends Mage_XmlConnect_Model_Preview_Abstract
{
    /**
     * Get application banner image url
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getBannerImage()
    {
        $configPath = 'conf/body/bannerImage';
        if ($this->getData($configPath)) {
            $bannerImage = $this->getData($configPath);
        } else {
            $bannerImage = $this->getPreviewImagesUrl('banner.png');
        }
        return $bannerImage;
    }

    /**
     * Get background image url according device type
     *
     * @return string
     */
    public function getBackgroundImage()
    {
        $configPath = 'conf/body/backgroundImage';
        $imageUrlOrig = $this->getData($configPath);
        if ($imageUrlOrig) {
            $backgroundImage = $imageUrlOrig;
        } else {
            $backgroundImage = $this->getPreviewImagesUrl('background.png');
        }
        return $backgroundImage;
    }
}
