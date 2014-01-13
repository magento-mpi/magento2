<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\File\FileList;

use Magento\ObjectManager;

/**
 * Factory that produces LESS file list instances
 */
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
     * Return newly created instance of a LESS file list
     *
     * @return \Magento\Less\File\FileList
     */
    public function create()
    {
        return $this->objectManager->create('Magento\Less\File\FileList');
    }
}
