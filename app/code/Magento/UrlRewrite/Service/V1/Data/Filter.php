<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Service\V1\Data;

/**
 * Url rewrite search filter
 */
class Filter
{
    /**
     * Field data
     */
    const FIELD_DATA = 'data';

    /**
     * Data with filter values
     *
     * @var array
     */
    protected $data = [];

    /**
     * Possible fields for filter
     *
     * @var array
     */
    protected $possibleFields = [
        UrlRewrite::ENTITY_ID,
        UrlRewrite::ENTITY_TYPE,
        UrlRewrite::STORE_ID,
        UrlRewrite::REQUEST_PATH,
        UrlRewrite::REDIRECT_TYPE,
        UrlRewrite::IS_AUTOGENERATED,
        self::FIELD_DATA,
    ];

    /**
     * Filter constructor
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data = [])
    {
        if ($data) {
            if ($wrongFields = array_diff(array_keys($data), $this->possibleFields)) {
                throw new \InvalidArgumentException(
                    sprintf('There is wrong fields passed to filter: "%s"', implode(', ', $wrongFields))
                );
            }
            $this->data = $data;
        }
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    protected function _set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
