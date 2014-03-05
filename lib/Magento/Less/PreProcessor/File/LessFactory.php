<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor\File;

/**
 * Factory class for \Magento\Less\PreProcessor\File\Less
 */
class LessFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $instanceName = 'Magento\Less\PreProcessor\File\Less'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\Less\PreProcessor\File\Less
     * @throws \UnexpectedValueException
     */
    public function create(array $data = array())
    {
        $fileLessProcessor = $this->objectManager->create($this->instanceName, $data);
        if (!$fileLessProcessor instanceof Less) {
            throw new \UnexpectedValueException(
                get_class($fileLessProcessor) . ' doesn\'t extend \Magento\Less\PreProcessor\File\Less'
            );
        }
        return $fileLessProcessor;
    }
}
