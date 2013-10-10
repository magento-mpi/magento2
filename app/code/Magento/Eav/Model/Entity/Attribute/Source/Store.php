<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer store_id attribute source
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Entity\Attribute\Source;

class Store extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Magento\Core\Model\Resource\Store\CollectionFactory
     */
    protected $_storeCollFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptCollFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Core\Model\Resource\Store\CollectionFactory $storeCollFactory
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptCollFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Core\Model\Resource\Store\CollectionFactory $storeCollFactory
    ) {
        parent::__construct($coreData, $attrOptCollFactory, $attrOptionFactory);
        $this->_storeCollFactory = $storeCollFactory;
    }

    /**
     * Retrieve Full Option values array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = $this->_storeCollFactory->create()
                ->load()
                ->toOptionArray();
        }
        return $this->_options;
    }
}
