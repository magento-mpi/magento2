<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api\Data;

interface Group
{
    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $value
     * @return $this
     */
    public function setId($value);

    /**
     * Get code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set string code
     *
     * @param $value
     * @return $this
     */
    public function setCode($value);

    /**
     * Get tax class id
     *
     * @return int
     */
    public function getTaxClassId();

    /**
     * Set tax class id
     *
     * @param int $value
     * @return $this
     */
    public function setTaxClassId($value);

}
