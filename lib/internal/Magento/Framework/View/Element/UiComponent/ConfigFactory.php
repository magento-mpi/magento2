<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Element\UiComponent;

use Magento\Framework\ObjectManager;

/**
 * Class ConfigFactory
 */
class ConfigFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName = null;

    /**
     * Factory constructor
     *
     * @param ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManager $objectManager,
        $instanceName = 'Magento\Framework\View\Element\UiComponent\ConfigInterface'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\Ui\Configuration
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}
