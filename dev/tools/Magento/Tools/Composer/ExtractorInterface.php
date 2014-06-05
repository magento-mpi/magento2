<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer;

use \Magento\Tools\Composer\Model\ArrayAndObjectAccess;

interface ExtractorInterface
{

    public function extract($collection = array(), &$count = 0);

    public function create(ArrayAndObjectAccess $definition);

    public function setValues(&$component, ArrayAndObjectAccess $definition);

    public function getType();

    public function getPath();

    public function getParser($filename);


}