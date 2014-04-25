<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Less\PreProcessor\File;

/**
 * Factory class for \Magento\Framework\Less\PreProcessor\File\Less
 */
class LessFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
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
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        $instanceName = 'Magento\Framework\Less\PreProcessor\File\Less'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\Framework\Less\PreProcessor\File\Less
     * @throws \UnexpectedValueException
     */
    public function create(array $data = array())
    {
        $fileLessProcessor = $this->objectManager->create($this->instanceName, $data);
        if (!$fileLessProcessor instanceof Less) {
            throw new \UnexpectedValueException(
                get_class($fileLessProcessor) . ' doesn\'t extend \Magento\Framework\Less\PreProcessor\File\Less'
            );
        }
        return $fileLessProcessor;
    }
}
