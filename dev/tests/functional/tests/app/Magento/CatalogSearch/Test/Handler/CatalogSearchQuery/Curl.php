<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Handler\CatalogSearchQuery;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Create new search term via curl.
 */
class Curl extends AbstractCurl implements CatalogSearchQueryInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'display_in_terms' => [
            'No' => 0,
        ],
        'store_id' => [
            'Main Website/Main Website Store/Default Store View' => 1
        ]
    ];

    /**
     * Search term url.
     *
     * @var string
     */
    protected $url = 'search/term/';

    /**
     * Post request for creating search term
     *
     * @param FixtureInterface $fixture|null [optional]
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->replaceMappingData($fixture->getData());
        $this->addNewSearchTerm($data);

        return ['id' => $this->getNewSearchTermId($data['query_text'])];
    }

    /**
     * Add new search term.
     *
     * @param array $data
     */
    protected function addNewSearchTerm(array $data)
    {
        $url = $_ENV['app_backend_url'] . $this->url . 'save';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $curl->read();
        $curl->close();
    }

    /**
     * Getting search term id.
     *
     * @param string $queryText
     * @return int
     * @throws \Exception
     */
    protected function getNewSearchTermId($queryText)
    {
        $filter = base64_encode('search_query=' . $queryText);
        $url = $_ENV['app_backend_url'] . $this->url . 'index/filter/' . $filter;
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        if (!preg_match('#' . $this->url . 'edit/id/(\d+)/"#', $response, $matches)) {
            throw new \Exception('Search term not found in grid!');
        }

        return (int)$matches[1];
    }
}
