<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture\CatalogProductSimple;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;

/**
 * Source for attributes field.
 */
class CustomAttribute implements FixtureInterface
{
    /**
     * Attribute name.
     *
     * @var string
     */
    protected $data;

    /**
     * Attribute fixture.
     *
     * @var CatalogProductAttribute
     */
    protected $attribute;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param CatalogProductAttribute $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, CatalogProductAttribute $data)
    {
        $this->params = $params;
        $this->data['value'] = $this->getDefaultAttributeValue($data);
        $this->data['type'] = $data->getFrontendInput();
        $this->data['code'] = $data->getAttributeCode();
        $this->attribute = $data;
    }

    /**
     * Get default value of custom attribute considering to it's type.
     *
     * @param CatalogProductAttribute $attribute
     * @return string
     */
    protected function getDefaultAttributeValue(CatalogProductAttribute $attribute)
    {
        $possibleFields = [
            'default_value_text',
            'default_value_textarea',
            'default_value_date',
            'default_value_yesno',
            'options'
        ];
        foreach ($possibleFields as $field) {
            if ($attribute->hasData($field) !== false) {
                $defaultValue = $attribute->getData($field);
                if (is_array($defaultValue)) {
                    foreach ($defaultValue as $option) {
                        if ($option['is_default'] == 'Yes') {
                            return $option['admin'];
                        }
                    }
                } else {
                    return $defaultValue;
                }
            } else {
                continue;
            }
        }
    }

    /**
     * Persist attribute options.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set.
     *
     * @param string|null $key
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return CatalogProductAttribute fixture.
     *
     * @return CatalogProductAttribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Return data set configuration settings.
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }
}
