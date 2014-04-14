<?php
/**
 * Value interface
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\App\Config;

interface ValueInterface
{
    /**
     * Table name
     */
    const ENTITY = 'config_data';

    /**
     * Check if config data value was changed
     * @todo this method should be make as protected
     * @return bool
     */
    public function isValueChanged();

    /**
     * Get old value from existing config
     *
     * @return string
     */
    public function getOldValue();

    /**
     * Get value by key for new user data from <section>/groups/<group>/fields/<field>
     *
     * @param string $key
     * @return string
     */
    public function getFieldsetDataValue($key);
}
