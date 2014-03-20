<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

interface AreaInterface
{
    const PART_CONFIG = 'config';
    const PART_TRANSLATE = 'translate';
    const PART_DESIGN = 'design';

    /**
     * Load area part
     *
     * @param string $partName
     * @return $this
     */
    public function load($partName = null);
}
