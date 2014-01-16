<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Admin search test connection controller
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Controller\Adminhtml\Search\System\Config;

use Magento\Backend\App\Action,
    Magento\Filesystem;

class Testconnection extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @param Action\Context $context
     * @param Filesystem $filesystem
     */
    public function __construct(Action\Context $context, Filesystem $filesystem)
    {
        parent::__construct($context);
        $this->filesystem = $filesystem;
    }


    /**
     * Check for connection to server
     */
    public function pingAction()
    {
        if (!isset($_REQUEST['host']) || !($host = $_REQUEST['host'])
            || !isset($_REQUEST['port']) || !($port = (int)$_REQUEST['port'])
            || !isset($_REQUEST['path']) || !($path = $_REQUEST['path'])
        ) {
            echo 0;
            die;
        }

        $path = $host . ':' . $port . '/' . $path . '/admin/ping';
        $httpResource = $this->filesystem->getRemoteResource($path, \Magento\Filesystem::HTTP);

        if (isset($_REQUEST['timeout'])) {
            $timeout = (int)$_REQUEST['timeout'];
            if ($timeout < 0) {
                $timeout = -1;
            }
        } else {
            $timeout = 0;
        }

        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'HEAD',
                    'timeout' => $timeout
                )
            )
        );

        // attempt a HEAD request to the solr ping page
        $ping = $httpResource->readFile($path, null, $context);

        // result is false if there was a timeout
        // or if the HTTP status was not 200
        if ($ping !== false) {
            echo 1;
        } else {
            echo 0;
        }
    }
}
