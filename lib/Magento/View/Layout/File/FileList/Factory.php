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
namespace Magento\View\Layout\File\FileList;

use Magento\ObjectManager;

class Factory
{
    /**
     * @var ObjectManager
     */
    private $_objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return newly created instance of a layout file list
     *
     * @return \Magento\View\Layout\File\FileList
     */
    public function create()
    {
        return $this->_objectManager->create('Magento\View\Layout\File\FileList');
    }
}
