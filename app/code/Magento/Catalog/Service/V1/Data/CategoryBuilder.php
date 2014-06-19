<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use Magento\Framework\Service\Data\Eav\AbstractObjectBuilder;
use Magento\Framework\Service\Data\Eav\AttributeValueBuilder;
use Magento\Catalog\Service\V1\Category\MetadataServiceInterface;

class CategoryBuilder extends AbstractObjectBuilder
{
    /**
     * @var MetadataServiceInterface
     */
    protected $metadataService;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        MetadataServiceInterface $metadataService
    )
    {
        parent::__construct($objectFactory, $valueBuilder);
        $this->metadataService = $metadataService;
    }

    /**
     * Template method used to configure the attribute codes for the category attributes
     *
     * @return string[]
     */
    public function getCustomAttributesCodes()
    {
        $attributeCodes = array();
        foreach ($this->metadataService->getCustomAttributesMetadata() as $attribute) {
            $attributeCodes[] = $attribute->getAttributeCode();
        }
        return $attributeCodes;
    }

    /**
     * Set category id
     *
     * @param  int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(Category::ID, $value);
    }

    /**
     * Set path of the category
     *
     * @param  string $value
     * @return $this
     */
    public function setPath($value)
    {
        return $this->_set(Category::PATH, $value);
    }

    /**
     * Set position of the category
     *
     * @param  int $value
     * @return $this
     */
    public function setPosition($value)
    {
        return $this->_set(Category::POSITION, $value);
    }

    /**
     * Set category level
     *
     * @param  int $value
     * @return $this
     */
    public function setLevel($value)
    {
        return $this->_set(Category::LEVEL, $value);
    }

    /**
     * Set category children count
     *
     * @param  int $value
     * @return $this
     */
    public function setChildrenCount($value)
    {
        return $this->_set(Category::CHILDREN_COUNT, $value);
    }

    /**
     * Set category created date
     *
     * @param  string $value
     * @throws \Magento\Framework\Exception\InputException
     */
    public function setCreatedAt($value)
    {
        throw new \Magento\Framework\Exception\InputException(
            'Field "created_at" is readonly',
            ['fieldName' => 'created_at']
        );
    }

    /**
     * Set category updated date
     *
     * @param  string $value
     * @throws \Magento\Framework\Exception\InputException
     */
    public function setUpdatedAt($value)
    {
        throw new \Magento\Framework\Exception\InputException(
            'Field "updated_at" is readonly',
            ['fieldName' => 'updated_at']
        );
    }
}

