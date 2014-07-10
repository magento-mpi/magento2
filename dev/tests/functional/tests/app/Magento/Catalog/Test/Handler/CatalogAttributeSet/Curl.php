<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\CatalogAttributeSet;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Catalog\Test\Fixture\CatalogAttributeSet;

/**
 * Class Curl
 * Create new Attribute Set via curl
 */
class Curl extends AbstractCurl implements CatalogAttributeSetInterface
{
    /**
     * Regex for finding attribute set id
     *
     * @var string
     */
    protected $attributeSetId = '`http.*?product_set\/delete\/id\/(\d*?)\/`';

    /**
     * Regex for finding attributes
     *
     * @var string
     */
    protected $attributes = '#buildCategoryTree\(this.root,.*?(\[.*\}\]\}\])\);#s';

    /**
     * Regex for finding attribute set name
     *
     * @var string
     */
    protected $attributeSetName = '#id="attribute_set_name".*?value="([\w\d]+)"#s';

    /**
     * Post request for creating Attribute Set
     *
     * @param FixtureInterface|null $fixture
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        /** @var CatalogAttributeSet $fixture */
        $response = $this->createAttributeSet($fixture);

        $attributeSetId = (!$fixture->hasData('attribute_set_id'))
            ? $this->getData($this->attributeSetId, $response)
            : 4;

        $assignedAttributes = $fixture->hasData('assigned_attributes')
            ? $fixture->getDataFieldConfig('assigned_attributes')['source']->getAttributes()
            : [];
        $dataAttribute = $this->getDataAttributes($response);

        foreach ($assignedAttributes as $assignedAttribute) {
            $dataAttribute['attributes'][] = [$assignedAttribute->getAttributeId(), $dataAttribute['groups'][0][0]];
        }

        $this->updateAttributeSet($attributeSetId, $dataAttribute);

        return ['attribute_set_id' => $attributeSetId];
    }

    /**
     * Create Attribute Set
     *
     * @param CatalogAttributeSet $fixture
     * @return string
     */
    protected
    function createAttributeSet(
        CatalogAttributeSet $fixture
    ) {
        $data = $fixture->getData();
        if (!isset($data['gotoEdit'])) {
            $data['gotoEdit'] = 1;
        }
        if (!isset($data['attribute_set_id'])) {
            $data['skeleton_set'] = $fixture
                ->getDataFieldConfig('skeleton_set')['source']
                ->getAttributeSet()
                ->getAttributeSetId();
        } else {
            if ($data['attribute_set_id'] == 4) {
                return $this->getDefaultAttributeSet();
            }
            $data['skeleton_set'] = $data['attribute_set_id'];
        }

        $url = $_ENV['app_backend_url'] . 'catalog/product_set/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        return $response;
    }

    /**
     * Get Default Attribute Set page with curl
     *
     * @return string
     */
    protected
    function getDefaultAttributeSet()
    {
        $url = $_ENV['app_backend_url'] . 'catalog/product_set/edit/id/4/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', []);
        $response = $curl->read();
        $curl->close();

        return $response;
    }

    /**
     * Update Attribute Set
     *
     * @param int $attributeSetId
     * @param array $dataAttribute
     * @return void
     */
    protected
    function updateAttributeSet(
        $attributeSetId,
        array $dataAttribute
    ) {
        $data = ['data' => json_encode($dataAttribute)];
        $url = $_ENV['app_backend_url'] . 'catalog/product_set/save/id/' . $attributeSetId . '/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $curl->read();
        $curl->close();
    }

    /**
     * Get data attributes for curl
     *
     * @param string $response
     * @return array
     */
    protected
    function getDataAttributes(
        $response
    ) {
        $attributes = $this->getData($this->attributes, $response, true);
        $dataAttribute = [];

        $index = 0;
        foreach ($attributes as $key => $parentAttributes) {
            $dataAttribute['groups'][$key][] = $parentAttributes['id'];
            $dataAttribute['groups'][$key][] = $parentAttributes['text'];
            $dataAttribute['groups'][$key][] = $key + 1;
            foreach ($parentAttributes['children'] as $attribute) {
                $dataAttribute['attributes'][$index][] = $attribute['id'];
                $dataAttribute['attributes'][$index][] = $parentAttributes['id'];
                $dataAttribute['attributes'][$index][] = $index;
                $dataAttribute['attributes'][$index][] = $attribute['entity_id'];
                $index = $index + 1;
            }
        }
        $dataAttribute['not_attributes'] = [];
        $dataAttribute['removeGroups'] = [];
        $dataAttribute['attribute_set_name'] = $this->getData($this->attributeSetName, $response);

        return $dataAttribute;
    }

    /**
     * Select data from response by regular expression
     *
     * @param string $regularExpression
     * @param string $response
     * @param bool $isJson
     * @return mixed
     * @throws \Exception
     */
    protected
    function getData(
        $regularExpression,
        $response,
        $isJson = false
    ) {
        preg_match($regularExpression, $response, $matches);
        if (!isset($matches[1])) {
            throw new \Exception("Can't find data in response by regular expression \"{$regularExpression}\".");
        }

        return $isJson ? json_decode($matches[1], true) : $matches[1];
    }
}
