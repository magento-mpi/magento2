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
     * Constructor
     *
     * @param \Magento\File\Transfer\Adapter\Http $transferAdapter
     * @param \Magento\Stdlib\Cookie              $cookie
     * @param \Magento\App\Http\Context           $context
     */
    public function __construct(
        \Magento\File\Transfer\Adapter\Http $transferAdapter,
        \Magento\Stdlib\Cookie $cookie,
        \Magento\App\Http\Context $context
    ) {
        $this->_transferAdapter = $transferAdapter;
        parent::__construct($cookie, $context);
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
