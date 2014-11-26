<?php
/**
 * Bundle Option Type Source Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Source\Option;

class Type extends \Magento\Framework\Model\AbstractExtensibleModel
    implements \Magento\Framework\Option\ArrayInterface, \Magento\Bundle\Api\Data\OptionTypeInterface
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\MetadataServiceInterface $metadataService
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $options
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\MetadataServiceInterface $metadataService,
        array $options,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    )
    {
        $this->options = $options;
        parent::__construct($context, $registry, $metadataService, $resource, $resourceCollection, $data);
    }

    /**
     * Get Bundle Option Type
     *
     * @return array
     */
    public function toOptionArray()
    {
        $types = array();
        foreach ($this->options as $value => $label) {
            $types[] = array('label' => $label, 'value' => $value);
        }
        return $types;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData('label');
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData('code');
    }
}
