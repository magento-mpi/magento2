<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Catalog\Setup;

use Magento\Framework\File\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\SetupInterface;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;

class Attribute implements SetupInterface
{
    protected $attributeFactory;

    protected $catalogConfig;

    protected $attrOptionCollectionFactory;

    protected $productHelper;

    protected $eavConfig;

    protected $fixtureHelper;

    protected $csvReaderFactory;

    public function __construct(
        \Magento\Catalog\Model\Resource\Eav\AttributeFactory $attributeFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Eav\Model\Config $eavConfig,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->catalogConfig = $catalogConfig;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->productHelper = $productHelper;
        $this->eavConfig = $eavConfig;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
    }

    public function run()
    {
        echo "Installing catalog attributes\n";

        $attributePrototype = $this->attributeFactory->create();

        $fileName = $this->fixtureHelper->getPath('Catalog/attributes.csv');
        $csvReader = $this->csvReaderFactory->create(array('fileName' => $fileName, 'mode' => 'r'));
        foreach($csvReader as $row) {

            $data = $row;

            $data['attribute_set'] = explode("\n", $data['attribute_set']);

            $attribute = $this->eavConfig->getAttribute('catalog_product', $data['attribute_code']);
            if (!$attribute) {
                $attribute = $attributePrototype;
                $attribute->unsetData();
            }

            $data['option'] = $this->getOption($attribute, $data);
            $data['source_model'] = $this->productHelper->getAttributeSourceModelByInputType($data['frontend_input']);
            $data['backend_model'] = $this->productHelper->getAttributeBackendModelByInputType($data['frontend_input']);
            $data += array('is_filterable' => 0, 'is_filterable_in_search' => 0, 'apply_to' => array());
            $data['backend_type'] = $attribute->getBackendTypeByInput($data['frontend_input']);

            $attribute->addData($data);
            $attribute->setIsUserDefined(1);

            $attribute->save();
            $attributeId = $attribute->getId();

            if (is_array($data['attribute_set'])) {
                foreach ($data['attribute_set'] as $setName) {
                    static $i = 0; $i++;

                    $attributeSetId = $this->catalogConfig->getAttributeSetId(4, $setName);
                    $attributeGroupId = $this->catalogConfig->getAttributeGroupId($attributeSetId, 'Product Details');

                    $attribute = $attributePrototype;
                    $attribute->unsetData();
                    $attribute
                        ->setId($attributeId)
                        ->setAttributeGroupId($attributeGroupId)
                        ->setAttributeSetId($attributeSetId)
                        ->setEntityTypeId(4)
                        ->setSortOrder($i + 999)
                        ->save();
                }
            }

            echo '.';
        }
        echo "\n";

        $this->eavConfig->clear();
    }

    protected function getOption($attribute, $data)
    {
        $result = [];
        $data['option'] = explode("\n", $data['option']);
        /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection $options */
        $options = $this->attrOptionCollectionFactory->create()
            ->setAttributeFilter($attribute->getId())
            ->setPositionOrder('asc', true)
            ->load();
        foreach ($data['option'] as $value) {
            if (!$options->getItemByColumnValue('value', $value)) {
                $result[] = $value;
            }
        }
        return $result ? $this->convertOption($result) : $result;
    }

    protected function convertOption($values)
    {
        $result = ['order' => [], 'value' => []];
        $i = 0;
        foreach ($values as $value) {
            $result['order']['option_' . $i] = (string)$i;
            $result['value']['option_' . $i] = [0 => $value, 1 => ''];
            $i++;
        }
        return $result;
    }
}
