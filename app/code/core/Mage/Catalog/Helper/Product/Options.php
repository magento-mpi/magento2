<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product Custom Options helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Product_Options extends Mage_Core_Helper_Abstract
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(Mage_Core_Helper_Context $context, Magento_Filesystem $filesystem)
    {
        parent::__construct($context);
        $this->_filesystem = $filesystem;
    }

    /**
     * Fetches and outputs file to user browser
     * $info is array with following indexes:
     *  - 'path' - full file path
     *  - 'type' - mime type of file
     *  - 'size' - size of file
     *  - 'title' - user-friendly name of file (usually - original name as uploaded in Magento)
     *
     * @param Mage_Core_Controller_Response_Http $response
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

            echo $this->_filesystem->read($filePath);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}
