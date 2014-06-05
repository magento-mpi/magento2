<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer;

/**
 * Interface ParserInterface
 */
interface ParserInterface
{

    /**
     * Retrieve mapping information for component
     *
     * @return array
     * @throws \ErrorException
     */
    public function getMappings();

}