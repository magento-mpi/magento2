<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\File\Storage;

use Magento\App\Response\Http;

class Response extends Http
{
    /**
     * @var \Magento\File\Transfer\Adapter\Http
     */
    protected $_transferAdapter;

    /**
     * Full path to file
     *
     * @var string
     */
    protected $_filePath;

    /**
     * @param \Magento\File\Transfer\Adapter\Http $transferAdapter
     * @param \Magento\Stdlib\Cookie $cookie
     */
    public function __construct(\Magento\File\Transfer\Adapter\Http $transferAdapter, \Magento\Stdlib\Cookie $cookie)
    {
        $this->_transferAdapter = $transferAdapter;
        parent::__construct($cookie);
    }

    /**
     * Send response
     */
    public function sendResponse()
    {
        if ($this->_filePath && $this->getHttpResponseCode() == 200) {
            $this->_transferAdapter->send($this->_filePath);
        } else {
            parent::sendResponse();
        }
    }

    /**
     * @param string $path
     */
    public function setFilePath($path)
    {
        $this->_filePath = $path;
    }
}
