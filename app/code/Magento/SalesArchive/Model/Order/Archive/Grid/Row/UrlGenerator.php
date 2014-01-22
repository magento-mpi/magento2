<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Archive Grid row url generator
 */
namespace Magento\SalesArchive\Model\Order\Archive\Grid\Row;

class UrlGenerator
    extends \Magento\Backend\Model\Widget\Grid\Row\UrlGenerator
{
    /**
     * @var $_authorizationModel \Magento\AuthorizationInterface
     */
    protected $_authorizationModel;

    /**
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\AuthorizationInterface $authorization
     * @param array $args
     */
    public function __construct(
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\AuthorizationInterface $authorization,
        array $args = array()
    ) {
        $this->_authorizationModel = $authorization;
        parent::__construct($backendUrl, $args);
    }

    /**
     * Generate row url
     * @param \Magento\Object $item
     * @return bool|string
     */
    public function getUrl($item)
    {
        if ($this->_authorizationModel->isAllowed('Magento_SalesArchive::orders')) {
            return parent::getUrl($item);
        }
        return false;
    }
}
