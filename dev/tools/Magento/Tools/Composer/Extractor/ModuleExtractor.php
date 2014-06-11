<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use Magento\Tools\Composer\Model\Package;

/**
 * Extractor for Modules
 */
class ModuleExtractor extends  ExtractorAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '/app/code/Magento/';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "magento2-module";
    }

}