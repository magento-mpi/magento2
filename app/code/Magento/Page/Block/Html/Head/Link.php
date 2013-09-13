<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Link page block
 *
 * @category   Magento
 * @package    Magento_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Page_Block_Html_Head_Link extends Magento_Core_Block_Template implements Magento_Page_Block_Html_Head_AssetBlock
{
    const VIRTUAL_CONTENT_TYPE = 'link';

    /**
     * Contructor
     *
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Page_Asset_RemoteFactory $remoteFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Page_Asset_RemoteFactory $remoteFactory,
        Magento_Core_Helper_Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->setAsset(
            $remoteFactory->create(array(
                'url' => (string)$this->getData('url'),
                'contentType' => self::VIRTUAL_CONTENT_TYPE,
            ))
        );
    }

    /**
     * Get block asset
     *
     * @return Magento_Core_Model_Page_Asset_AssetInterface
     */
    public function getAsset()
    {
        return $this->_getData('asset');
    }
}
