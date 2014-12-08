<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Composer\Json\JsonFile;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Package\Version\VersionParser;
use Magento\Framework\App\Filesystem\DirectoryList;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Magento\Setup\Model\PhpExtensions;
use Magento\Setup\Model\FilePermissions;

class Environment extends AbstractActionController
{
    /**
     * List of required php extensions.
     *
     * @var \Magento\Setup\Model\PhpExtensions
     */
    protected $extensions;

    /**
     * Directory List to read composer.json
     *
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * Version parser
     *
     * @var VersionParser
     */
    protected $versionParser;

    /**
     * Constructor
     *
     * @param PhpExtensions $extensions
     * @param FilePermissions $permissions
     * @param DirectoryList $directoryList
     * @param VersionParser $versionParser
     */
    public function __construct(
        PhpExtensions $extensions,
        FilePermissions $permissions,
        DirectoryList $directoryList,
        VersionParser $versionParser
    ) {
        $this->extensions = $extensions;
        $this->permissions = $permissions;
        $this->directoryList = $directoryList;
        $this->versionParser = $versionParser;
    }

    /**
     * Verifies php version
     *
     * @return JsonModel
     */
    public function phpVersionAction()
    {
        $jsonFile = (new JsonFile($this->directoryList->getRoot() . '/composer.json'))->read();
        $multipleConstraints = $this->versionParser->parseConstraints($jsonFile['require']['php']);
        $currentPhpVersion = new VersionConstraint('=', PHP_VERSION);
        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;
        if (!$multipleConstraints->matches($currentPhpVersion)) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
        }

        $data = [
            'responseType' => $responseType,
            'data' => [
                'required' => $jsonFile['require']['php'],
                'current' => PHP_VERSION,
            ],
        ];
        return new JsonModel($data);
    }

    /**
     * Verifies php extensions
     *
     * @return JsonModel
     */
    public function phpExtensionsAction()
    {
        $required = $this->extensions->getRequired();
        $current = $this->extensions->getCurrent();

        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;
        $missing = array_values(array_diff($required, $current));
        if ($missing) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
        }

        $data = [
            'responseType' => $responseType,
            'data' => [
                'required' => $required,
                'missing' => $missing,
            ],
        ];

        return new JsonModel($data);
    }

    /**
     * Verifies file permissions
     *
     * @return JsonModel
     */
    public function filePermissionsAction()
    {
        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;
        if ($this->permissions->getMissingWritableDirectoriesForInstallation()) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
        }

        $data = [
            'responseType' => $responseType,
            'data' => [
                'required' => $this->permissions->getInstallationWritableDirectories(),
                'current' => $this->permissions->getInstallationCurrentWritableDirectories(),
            ],
        ];

        return new JsonModel($data);
    }
}
