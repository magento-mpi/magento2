<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory that produces layout file list instances
 */
namespace Magento\Core\Model\Layout\File\FileList;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return newly created instance of a layout file list
     *
     * @return \Magento\Core\Model\Layout\File\ListFile
     */
    public function create()
    {
        return $this->_objectManager->create('Magento\Core\Model\Layout\File\ListFile');
    }
}
