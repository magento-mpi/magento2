<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\Download as ModelDownload;
use Magento\Catalog\Model\Product\Type\AbstractType as AbstractProductType;

/**
 * Sales controller for download purposes
 */
class Download extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Model\Download
     */
    protected $_download;

    /**
     * @param Context $context
     * @param ModelDownload $download
     */
    public function __construct(Context $context, ModelDownload $download)
    {
        $this->_download = $download;
        parent::__construct($context);
    }
}
