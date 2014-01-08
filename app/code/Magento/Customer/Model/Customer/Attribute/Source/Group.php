<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer group attribute source
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Customer\Attribute\Source;

class Group extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService;

    /**
     * @var \Magento\Convert\Object
     */
    protected $_converter;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptCollFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param \Magento\Convert\Object $converter
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptCollFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        \Magento\Convert\Object $converter
    ) {
        $this->_groupService = $groupService;
        $this->_converter = $converter;
        parent::__construct($coreData, $attrOptCollFactory, $attrOptionFactory);
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            $groups = $this->_groupService->getGroups(FALSE);
            $this->_options = $this->_converter->toOptionArray($groups, 'id', 'code');
        }
        return $this->_options;
    }
}
