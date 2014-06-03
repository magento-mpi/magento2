<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Structure;

interface SearchInterface
{
    /**
     * Find element by path
     *
     * @param string $path
     * @return ElementInterface|null
     */
    public function getElement($path);
}
