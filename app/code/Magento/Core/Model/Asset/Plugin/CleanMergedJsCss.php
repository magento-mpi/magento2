<?php
/**
 * Dispatch exception handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\FrontController\Plugin;

class CleanMergedJsCss
{
    /**
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $database;

    /**
     * @param \Magento\Core\Helper\File\Storage\Database $database
     */
    public function __construct(\Magento\Core\Helper\File\Storage\Database $database)
    {
        $this->database = $database;
    }

    /**
     * Clean files in database on cleaning merged assets
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     */
    public function aroundCleanMergedJsCss(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $invocationChain->proceed($arguments);

        $this->database->deleteFolder($arguments['mergedDir']);
    }
}
