<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Magento\Setup\Model\PhpExtensions;
use Magento\Setup\Model\FilePermissions;

class Environment extends AbstractActionController
{
    /**
     * The minimum required version of PHP
     */
    const PHP_VERSION_MIN = '5.4.0';

    /**
     * @var \Magento\Setup\Model\PhpExtensions
     */
    protected $extensions;

    /**
     * @param PhpExtensions $extensions
     * @param FilePermissions $permissions
     */
    public function __construct(PhpExtensions $extensions, FilePermissions $permissions)
    {
        $this->extensions = $extensions;
        $this->permissions = $permissions;
    }

    /**
     * @return JsonModel
     */
    public function phpVersionAction()
    {
        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;
        if (version_compare(PHP_VERSION, self::PHP_VERSION_MIN, '<') === true) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
        }

        $data = [
            'responseType' => $responseType,
            'data' => [
                'required' => self::PHP_VERSION_MIN,
                'current' => PHP_VERSION,
            ],
        ];
        return new JsonModel($data);
    }

    /**
     * @return JsonModel
     */
    public function phpExtensionsAction()
    {
        $required = $this->extensions->getRequired();
        $current = $this->extensions->getCurrent();

        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;
        if (array_diff($required, $current)) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
        }

        $data = [
            'responseType' => $responseType,
            'data' => [
                'required' => $required,
                'current' => $current,
            ],
        ];

        return new JsonModel($data);
    }

    /**
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
