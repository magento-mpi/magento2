<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model\EntryPoint;

class Shell extends \Magento\Core\Model\EntryPointAbstract
{
    /**
     * Filename of the entry point script
     *
     * @var string
     */
    protected $_entryFileName;

    /**
     * @param \Magento\Core\Model\Config\Primary $config
     * @param string $entryFileName  filename of the entry point script
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Core\Model\Config\Primary $config,
        $entryFileName,
        \Magento\ObjectManager $objectManager = null
    ) {
        parent::__construct($config, $objectManager);
        $this->_entryFileName = $entryFileName;
    }

    /**
     * Process request to application
     */
    protected function _processRequest()
    {
        /** @var $shell \Magento\Log\Model\Shell */
        $shell = $this->_objectManager->create('Magento\Log\Model\Shell', array('entryPoint' => $this->_entryFileName));
        $shell->run();
    }
}
