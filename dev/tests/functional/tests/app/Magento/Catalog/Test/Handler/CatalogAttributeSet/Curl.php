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

/**
 * Class Curl
 * Create new Attribute Set via curl
 */
class Curl extends AbstractCurl implements CatalogAttributeSetInterface
{
    /**
     * Post request for creating Attribute Set
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $fixture->getData();
        if (!isset($data['gotoEdit'])) {
            $data['gotoEdit'] = 1;
        }
        $data['skeleton_set'] = $fixture
            ->getDataFieldConfig('skeleton_set')['source']
            ->getAttributeSet()
            ->getAttributeSetId();

        $url = $_ENV['app_backend_url'] . 'catalog/product_set/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();

        list($tree1, $tree2) = explode('tree2.setRootNode(this.root2)', $response);
        preg_match_all('|id...(\d+)|', $tree2, $matches);
        $attributeId = max($matches[1]);

        $id = $this->getData('`http.*?id\/(\d*?)\/.*?data-ui-id=\"page-actions-toolbar-delete-button\".*`', $tree1);
        $attributes = $this->getData('#buildCategoryTree\(this.root,.*?(\[.*\}\]\}\])\);#s', $tree1, true);

        if ($attributes !== null) {
            $dataAttribute = $this->prepareDataAttribute($attributes);
        }
        $attributeSetName = $this->getData('#id="attribute_set_name".*?value="([\w\d]+)"#s', $response);
        $dataAttribute['attribute_set_name'] = $attributeSetName;
        $dataAttribute['attributes'][] = [$attributeId, $dataAttribute['groups'][0][0], 50, 2000];

        $data = ['data' => json_encode($dataAttribute)];
        $url = $_ENV['app_backend_url'] . 'catalog/product_set/save/id/' . $id . '/';
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $curl->read();
        $curl->close();

        return ['attribute_set_id' => $id];
    }

    /**
     * Prepare data for curl
     *
     * @param array $attributes
     * @return array
     */
    public function prepareDataAttribute($attributes)
    {
        $index = 0;
        $dataAttribute = [];
        foreach ($attributes as $key1 => $parentAttributes) {
            $dataAttribute['groups'][$key1][] = $parentAttributes['id'];
            $dataAttribute['groups'][$key1][] = $parentAttributes['text'];
            $dataAttribute['groups'][$key1][] = $key1 + 1;
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

        return $dataAttribute;
    }

    /**
     * Return data about regular expression
     *
     * @param string $regularExpression
     * @param string $response
     * @param bool $isJson
     * @return mixed|null
     */
    public function getData($regularExpression, $response, $isJson = false)
    {
        preg_match($regularExpression, $response, $matches);
        $result = isset($matches[1]) ? $matches[1] : null;
        if ($isJson) {
            $result = json_decode($matches[1], true);
        }

        return $result;
    }
}
