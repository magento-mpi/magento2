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
     * Default publisher file class
     */
    const DEFAULT_FILE_INSTANCE_CLASS = 'Magento\View\Publisher\File';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $instanceName;

    /**
     * @var array
     */
    protected $publisherFileTypes = [
        'css' => 'Magento\View\Publisher\CssFile'
    ];

    /**
     * @param ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(ObjectManager $objectManager, $instanceName = self::DEFAULT_FILE_INSTANCE_CLASS)
    {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Return newly created instance of a publisher file
     *
     * @param string $filePath
     * @param array $viewParams
     * @param null $sourcePath
     * @throws \UnexpectedValueException
     * @return FileInterface
     */
    public function create($filePath, array $viewParams, $sourcePath = null)
    {
        $instanceName = $this->instanceName;
        $extension = $this->getExtension($filePath);
        if (isset($this->publisherFileTypes[$extension])) {
            $instanceName = $this->publisherFileTypes[$extension];
        }
        $publisherFile = $this->objectManager->create(
            $instanceName,
            [
                'filePath'   => $filePath,
                'viewParams' => $viewParams,
                'sourcePath' => $sourcePath
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
