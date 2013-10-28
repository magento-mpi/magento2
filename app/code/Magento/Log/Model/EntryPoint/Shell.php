<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model\EntryPoint;

class Shell extends \Magento\App\AbstractEntryPoint
{
    /**
     * Filename of the entry point script
     *
     * @var string
     */
    protected $_entryFileName;

    /**
     * @param string $baseDir
     * @param array $params
     * @param \Magento\ObjectManager\ObjectManager $entryFileName
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        $baseDir,
        $params,
        $entryFileName,
        \Magento\ObjectManager $objectManager = null
    ) {
        parent::__construct($baseDir, $params, $objectManager);
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
