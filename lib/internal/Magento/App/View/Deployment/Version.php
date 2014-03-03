<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\App\View\Deployment;

/**
 * Deployment version of static files
 */
class Version
{
    /**
     * @var \Magento\App\State
     */
    private $appState;

    /**
     * @var Version\StorageInterface
     */
    private $versionStorage;

    /**
     * @var Version\GeneratorInterface
     */
    private $versionGenerator;

    /**
     * @param \Magento\App\State $appState
     * @param Version\StorageInterface $versionStorage
     * @param Version\GeneratorInterface $versionGenerator
     */
    public function __construct(
        \Magento\App\State $appState,
        Version\StorageInterface $versionStorage,
        Version\GeneratorInterface $versionGenerator
    ) {
        $this->appState = $appState;
        $this->versionStorage = $versionStorage;
        $this->versionGenerator = $versionGenerator;
    }

    /**
     * Retrieve deployment version of static files depending on the application mode
     *
     * @return string
     */
    public function getValue()
    {
        switch ($this->appState->getMode()) {
            case \Magento\App\State::MODE_DEFAULT:
                try {
                    $result = $this->versionStorage->load();
                } catch (\UnexpectedValueException $e) {
                    $result = $this->versionGenerator->generate();
                    $this->versionStorage->save($result);
                }
                break;

            case \Magento\App\State::MODE_DEVELOPER:
                $result = $this->versionGenerator->generate();
                break;

            default:
                $result = $this->versionStorage->load();
        }
        return $result;
    }
}
