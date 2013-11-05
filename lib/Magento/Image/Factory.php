<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Image;

use Magento\ObjectManager;

class Factory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Return \Magento\Image
     *
     * @param string $fileName
     * @return \Magento\Image
     */
    public function create($fileName = null)
    {
        return $this->objectManager->create('\Magento\Image', array('fileName' => $fileName));
    }
}
