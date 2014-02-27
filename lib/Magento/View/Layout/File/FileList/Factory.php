<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\FileList;

use Magento\ObjectManager;

/**
 * Factory that produces layout file list instances
 */
class Factory
{
    /**
     * Default file list collator
     */
    const FILE_LIST_COLLATOR = 'Magento\View\Layout\File\FileList\Collator';

    /**
     * Object manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Return newly created instance of a layout file list
     *
     * @param string $instanceName
     * @return \Magento\View\Layout\File\FileList
     * @throws \UnexpectedValueException
     */
    public function create($instanceName = self::FILE_LIST_COLLATOR)
    {
        $collator = $this->objectManager->get($instanceName);
        if (!$collator instanceof CollateInterface) {
            throw new \UnexpectedValueException("$instanceName has to implement the collate interface.");
        }
        return $this->objectManager->create('Magento\View\Layout\File\FileList', array('collator' => $collator));
    }
}
