<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

use Magento\ObjectManager;

/**
 * Publisher file factory
 */
class FileFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $publisherFileTypes = [
        'css' => 'Magento\View\Publisher\CssFile'
    ];

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Return newly created instance of a publisher file
     *
     * @param string $filePath
     * @param array $viewParams
     * @return FileInterface
     * @throws \UnexpectedValueException
     */
    public function create($filePath, array $viewParams)
    {
        $instanceName = 'Magento\View\Publisher\File';
        $extension = $this->getExtension($filePath);
        if (isset($this->publisherFileTypes[$extension])) {
            $instanceName = $this->publisherFileTypes[$extension];
        }
        $publisherFile = $this->objectManager->create(
            $instanceName,
            [
                'filePath' => $filePath,
                'viewParams' => $viewParams,
                'extension' => $extension
            ]
        );

        if (!$publisherFile instanceof FileInterface) {
            throw new \UnexpectedValueException("$instanceName has to implement the publisher file interface.");
        }
        return $publisherFile;
    }

    /**
     * Get file extension by file path
     *
     * @param string $filePath
     * @return string
     */
    protected function getExtension($filePath)
    {
        return strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    }
}
