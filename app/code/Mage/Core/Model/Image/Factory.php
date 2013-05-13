<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Image_Factory
{
    /**
     * @var Mage_Core_Model_Image_AdapterFactory
     */
    protected $_adapterFactory;

    /**
     * @param Mage_Core_Model_Image_AdapterFactory $adapterFactory
     */
    public function __construct(Mage_Core_Model_Image_AdapterFactory $adapterFactory)
    {
        $this->_adapterFactory = $adapterFactory;
    }

    /**
     * Return Varien_Image
     *
     * @param string $fileName
     * @param string $adapterType
     * @return Varien_Image
     */
    public function create($fileName = null, $adapterType = null)
    {
        $adapter = $this->_adapterFactory->create($adapterType);
        return new Varien_Image($adapter, $fileName);
    }
}
