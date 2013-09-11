<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Image;

class Factory
{
    /**
     * @var \Magento\Core\Model\Image\AdapterFactory
     */
    protected $_adapterFactory;

    /**
     * @param \Magento\Core\Model\Image\AdapterFactory $adapterFactory
     */
    public function __construct(\Magento\Core\Model\Image\AdapterFactory $adapterFactory)
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
