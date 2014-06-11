<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Model\Package;

/**
 * Extractor for Framework
 */
class FrameworkExtractor extends  ExtractorAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return "/lib/Magento/";
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "magento2-framework";
    }


}