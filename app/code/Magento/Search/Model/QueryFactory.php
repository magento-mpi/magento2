<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManager;
use Magento\Framework\Stdlib\String as StdlibString;
use Magento\Store\Model\ScopeInterface;

class QueryFactory implements QueryFactoryInterface
{
    /**
     * Query variable
     */
    const QUERY_VAR_NAME = 'q';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var Query
     */
    private $query;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var StdlibString
     */
    private $string;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Context $context
     * @param ObjectManager $objectManager
     * @param StdlibString $string
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ObjectManager $objectManager,
        StdlibString $string,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $context->getRequest();
        $this->objectManager = $objectManager;
        $this->string = $string;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        if (!$this->query) {
            $this->query = $this->create();
        }
        return $this->query;
    }

    /**
     * @return Query
     */
    private function create()
    {
        $maxQueryLength = $this->getMaxQueryLength();
        $rawQueryText = $this->getRawQueryText();
        $preparedQueryText = $this->getPreparedQueryText($rawQueryText, $maxQueryLength);
        /** @var \Magento\Search\Model\Query $query */
        $query = $this->objectManager->create('\Magento\Search\Model\Query')->loadByQuery($preparedQueryText);
        if (!$query->getId()) {
            $query->setQueryText($preparedQueryText);
        }
        $query->setIsQueryTextExceeded($this->isQueryTooLong($preparedQueryText, $maxQueryLength));
        return $query;
    }

    /**
     * Retrieve search query text
     *
     * @return string
     */
    private function getRawQueryText()
    {
        $queryText = $this->request->getParam(self::QUERY_VAR_NAME);
        return ($queryText === null || is_array($queryText))
            ? ''
            : $this->string->cleanString(trim($queryText));
    }

    /**
     * @param string $queryText
     * @param int|string $maxQueryLength
     * @return string
     */
    private function getPreparedQueryText($queryText, $maxQueryLength)
    {
        if ($this->isQueryTooLong($queryText, $maxQueryLength)) {
            $queryText = $this->string->substr($queryText, 0, $maxQueryLength);
        }
        return $queryText;
    }

    /**
     * Retrieve maximum query length
     *
     * @param mixed $store
     * @return int|string
     */
    private function getMaxQueryLength($store = null)
    {
        return $this->scopeConfig->getValue(
            Query::XML_PATH_MAX_QUERY_LENGTH,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $queryText
     * @param int|string $maxQueryLength
     * @return bool
     */
    private function isQueryTooLong($queryText, $maxQueryLength)
    {
        return ($maxQueryLength !== '' && $this->string->strlen($queryText) > $maxQueryLength);
    }
}
