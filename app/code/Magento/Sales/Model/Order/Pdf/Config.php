<?php
/**
 * Pdf config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Pdf;

class Config
{
    /** @var \Magento\Config\DataInterface */
    protected $_dataStorage;

    /**
     * @param \Magento\Config\DataInterface $dataStorage
     */
    public function __construct(\Magento\Config\DataInterface $dataStorage)
    {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * Get renderer configuration data by type
     *
     * @param string $pageType
     * @return array
     */
    public function getRenderersPerProduct($pageType)
    {
        return $this->_dataStorage->get("renderers/$pageType", array());
    }

    /**
     * Get list of settings for showing totals in PDF
     *
     * @return array
     */
    public function getTotals()
    {
        return $this->_dataStorage->get('totals', array());
    }
}
