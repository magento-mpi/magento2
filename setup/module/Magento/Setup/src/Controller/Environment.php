<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Package\Version\VersionParser;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Magento\Setup\Model\PhpVerifications;
use Magento\Setup\Model\FilePermissions;

class Environment extends AbstractActionController
{
    /**
     * List of current and required php verifications.
     *
     * @var \Magento\Setup\Model\PhpVerifications
     */
    protected $verifications;

    /**
     * Version parser
     *
     * @var VersionParser
     */
    protected $versionParser;

    /**
     * Missing composer.lock file message
     */
    const NO_COMPOSER_ERROR = 'Whoops, it looks like composer.lock file does not exist or is not readable. Please
        make sure to run `composer install` in the root directory. Additional details: ';

    /**
     * Constructor
     *
     * @param PhpVerifications $verifications
     * @param FilePermissions $permissions
     * @param VersionParser $versionParser
     */
    public function __construct(
        PhpVerifications $verifications,
        FilePermissions $permissions,
        VersionParser $versionParser
    ) {
        $this->verifications = $verifications;
        $this->permissions = $permissions;
        $this->versionParser = $versionParser;
    }

    /**
     * Verifies php version
     *
     * @return JsonModel
     */
    public function phpVersionAction()
    {
        try{
            $requiredVersion = $this->verifications->getPhpVersion();
        }catch (\Exception $e) {
            return new JsonModel(
                [
                    'responseType' => ResponseTypeInterface::RESPONSE_TYPE_ERROR,
                    'data' => [
                        'error' => 'phpVersionError',
                        'message' => self::NO_COMPOSER_ERROR . $e->getMessage()
                    ],
                ]
            );
        }
        $multipleConstraints = $this->versionParser->parseConstraints($requiredVersion);
        $currentPhpVersion = new VersionConstraint('=', PHP_VERSION);
        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;
        if (!$multipleConstraints->matches($currentPhpVersion)) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
        }
        $data = [
            'responseType' => $responseType,
            'data' => [
                'required' => isset($requiredVersion) ? $requiredVersion : '' ,
                'current' => PHP_VERSION,
            ],
        ];
        return new JsonModel($data);
    }

    /**
     * Verifies php verifications
     *
     * @return JsonModel
     */
    public function phpVerificationsAction()
    {
        try{
            $required = $this->verifications->getRequired();
            $current = $this->verifications->getCurrent();

        } catch (\Exception $e) {
            return new JsonModel(
                [
                    'responseType' => ResponseTypeInterface::RESPONSE_TYPE_ERROR,
                    'data' => [
                        'error' => 'phpExtensionError',
                        'message' => self::NO_COMPOSER_ERROR . $e->getMessage()
                    ],
                ]
            );
        }
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
