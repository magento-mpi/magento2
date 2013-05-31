<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Image_AdapterFactory
{
    const ADAPTER_GD    = 'GD';
    const ADAPTER_GD2   = 'GD2';
    const ADAPTER_IM    = 'IMAGEMAGICK';
    const ADAPTER_IME   = 'IMAGEMAGICK_EXTERNAL';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Helper_Data $helper
     */
    public function __construct(Magento_ObjectManager $objectManager, Mage_Core_Helper_Data $helper)
    {
        $this->_objectManager = $objectManager;
        $this->_helper = $helper;
    }

    /**
     * Return specified image adapter
     *
     * @param string $adapterType
     * @return Varien_Image_Adapter_Abstract
     * @throws InvalidArgumentException
     */
    public function create($adapterType = null)
    {
        if (!isset($adapterType)) {
           $adapterType = $this->_helper->getImageAdapterType();
        }
        $adapterClasses = array(
            self::ADAPTER_GD => 'Varien_Image_Adapter_Gd',
            self::ADAPTER_GD2 => 'Varien_Image_Adapter_Gd2',
            self::ADAPTER_IM => 'Varien_Image_Adapter_ImageMagick',
            self::ADAPTER_IME => 'Varien_Image_Adapter_ImageMagickExternal',
        );
        if (!isset($adapterClasses[$adapterType])) {
            throw new InvalidArgumentException(
                Mage::helper('Mage_Core_Helper_Data')->__('Invalid adapter selected.')
            );
        }
        return $this->_objectManager->create($adapterClasses[$adapterType]);
    }
}
