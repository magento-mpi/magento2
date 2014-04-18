<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Price;

/**
 * Class Pool
 */
class Pool implements \Iterator, \ArrayAccess
{
    /**
     * @var \Magento\Pricing\Price\PriceInterface[]
     */
    protected $prices;

    /**
     * @param array $prices
     * @param \Iterator $target
     */
    public function __construct(
        array $prices,
        \Iterator $target = null
    ) {
        $this->prices = $prices;
        foreach($target ?: [] as $code => $class) {
            if (empty($this->prices[$code])) {
                $this->prices[$code] = $class;
            }
        }
    }

    /**
     * Reset the Collection to the first element
     *
     * @return mixed|void
     */
    public function rewind()
    {
        return reset($this->prices);
    }

    /**
     * Return the current element
     *
     * @return \Magento\Pricing\Price\PriceInterface
     */
    public function current()
    {
        return current($this->prices);
    }

    /**
     * Return the key of the current element
     *
     * @return string
     */
    public function key()
    {
        return key($this->prices);
    }

    /**
     * Move forward to next element
     *
     * @return mixed|void
     */
    public function next()
    {
        return next($this->prices);
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return (bool)$this->key();
    }

    /**
     * Returns price class by code
     *
     * @param string $code
     * @return \Magento\Pricing\Price\PriceInterface
     * @throws \Magento\Pricing\Exception
     */
    public function get($code)
    {
        return $this->prices[$code];
    }

    /**
     * The value to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->prices[] = $value;
        } else {
            $this->prices[$offset] = $value;
        }
    }

    /**
     * The return value will be casted to boolean if non-boolean was returned.
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->prices[$offset]);
    }

    /**
     * The offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->prices[$offset]);
    }

    /**
     * The offset to retrieve.
     *
     * @param mixed $offset
     * @return PriceInterface|mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->prices[$offset]) ? $this->prices[$offset] : null;
    }
}
