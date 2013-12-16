<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product Custom Options helper
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Helper\Product;

class Options extends \Magento\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directory;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(\Magento\App\Helper\Context $context, \Magento\Filesystem $filesystem)
    {
        parent::__construct($context);
        $this->directory = $filesystem->getDirectoryRead(\Magento\Filesystem::ROOT);
    }

    /**
     * Fetches and outputs file to user browser
     * $info is array with following indexes:
     *  - 'path' - full file path
     *  - 'type' - mime type of file
     *  - 'size' - size of file
     *  - 'title' - user-friendly name of file (usually - original name as uploaded in Magento)
     *
     * @param \Magento\App\ResponseInterface $response
     * @param string $filePath
     * @param array $info
     * @return bool
     */
    public function downloadFileOption($response, $filePath, $info)
    {
        try {
            $response->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $info['type'], true)
                ->setHeader('Content-Length', $info['size'])
                ->setHeader('Content-Disposition', 'inline' . '; filename='.$info['title'])
                ->clearBody();
            $response->sendHeaders();

            echo $this->directory->readFile($this->directory->getRelativePath($filePath));
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
