<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
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
     * @return \Magento\Backend\Model\Config\Structure\ElementInterface|null
     */
    public function getElement($path);
}
