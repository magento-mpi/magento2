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
     * Return newly created instance of a layout file list
     *
     * @param string $collator
     * @return \Magento\View\Layout\File\FileList
     */
    public function create($collator = self::FILE_LIST_COLLATOR)
    {
        return $this->objectManager->create(
            'Magento\View\Layout\File\FileList',
            array('collator' => $this->objectManager->get($collator))
        );
    }
}
