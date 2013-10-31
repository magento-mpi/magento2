<?php
/**
 * Factory that produces layout file list instances
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\FileList;

use Magento\ObjectManager;

class Factory
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Return newly created instance of a layout file list
     *
     * @return \Magento\View\Layout\File\FileList
     */
    public function create()
    {
        return $this->objectManager->create('Magento\View\Layout\File\FileList');
    }
}
