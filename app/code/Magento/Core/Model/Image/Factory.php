<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Image_Factory
{
    /**
     * @var Magento_Core_Model_Image_AdapterFactory
     */
    protected $_adapterFactory;

    /**
     * @param Magento_Core_Model_Image_AdapterFactory $adapterFactory
     */
    public function __construct(Magento_Core_Model_Image_AdapterFactory $adapterFactory)
    {
        $this->_adapterFactory = $adapterFactory;
    }

    /**
     * Return \Magento\Image
     *
     * @param string $fileName
     * @param string $adapterType
     * @return \Magento\Image
     */
    public function create($fileName = null, $adapterType = null)
    {
        $adapter = $this->_adapterFactory->create($adapterType);
        return new \Magento\Image($adapter, $fileName);
    }
}
