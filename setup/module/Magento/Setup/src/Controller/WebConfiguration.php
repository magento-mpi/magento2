<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Magento\Framework\App\Filesystem\DirectoryList;

class WebConfiguration extends AbstractActionController
{
    /**
     * Directory list
     *
     * @var DirectoryList
     */
    private $dirList;

    /**
     * Constructor
     *
     * @param DirectoryList $dirList
     */
    public function __construct(DirectoryList $dirList)
    {
        $this->dirList = $dirList;
    }

    /**
     * Displays web configuration form
     *
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $projectRoot = $this->dirList->getRoot();
        $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        if (false === strpos($projectRoot . '/', $docRoot . '/')) { // if project root is outside of current doc root
            $urlPath = '';
        } else {
            $urlPath = substr($docRoot, strlen($projectRoot));
        }
        $view = new ViewModel(['autoBaseUrl' => 'http://' . $_SERVER['HTTP_HOST'] . $urlPath . '/']);
        $view->setTerminal(true);
        return $view;
    }
}
