<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\EntryPoint;

class Shell extends \Magento\Core\Model\EntryPointAbstract
{
    /**
     * Filename of the entry point script
     *
     * @var string
     */
    protected $_entryFileName;

    /**
     * @var \Magento\Index\Model\EntryPoint\Shell\ErrorHandler
     */
    protected $_errorHandler;

    /**
     * @param string $entryFileName filename of the entry point script
     * @param \Magento\Index\Model\EntryPoint\Shell\ErrorHandler $errorHandler
     * @param \Magento\Core\Model\Config\Primary $config
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        $entryFileName,
        \Magento\Index\Model\EntryPoint\Shell\ErrorHandler $errorHandler,
        \Magento\Core\Model\Config\Primary $config,
        \Magento\ObjectManager $objectManager = null
    ) {
        parent::__construct($config, $objectManager);
        $this->_entryFileName = $entryFileName;
        $this->_errorHandler = $errorHandler;
    }

    /**
     * Process request to application
     */
    protected function _processRequest()
    {
        /** @var $shell \Magento\Index\Model\Shell */
        $shell = $this->_objectManager
            ->create('Magento\Index\Model\Shell', array('entryPoint' => $this->_entryFileName));
        $shell->run();
        if ($shell->hasErrors()) {
            $this->_errorHandler->terminate(1);
        }
    }
}
