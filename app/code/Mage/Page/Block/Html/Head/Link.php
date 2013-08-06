<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Link page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Block_Html_Head_Link extends Mage_Core_Block_Template implements Mage_Page_Block_Html_Head_AssetBlock
{
    const VIRTUAL_CONTENT_TYPE = 'link';

    /** @var  Mage_Core_Model_Page_Asset_RemoteFactory */
    protected $remoteFactory;

    /**
     * Contructor
     *
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_Page_Asset_RemoteFactory $remoteFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Page_Asset_RemoteFactory $remoteFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
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
     * @return Mage_Core_Model_Page_Asset_AssetInterface
     */
    public function getAsset()
    {
        return $this->_getData('asset');
    }
}
