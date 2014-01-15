<?php
/**
 * Scoped Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\Scope;

class FactoryInterface
{
    /**
     * Create Scope class instance
     *
     * @param array $data
     * @return \IteratorAggregate
     */
    public function create(array $data = array());
}
