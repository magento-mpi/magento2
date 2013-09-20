<?php
/**
 * Set of tests of layout directives handling behavior
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\DataService\LayoutTest\Magento\Catalog\Service;

class TestProduct
{
    /**
     * Provide test product data fixture
     */
    public function getTestProduct($someArgName)
    {
        return array(
            'testProduct' => array(
                'id' => $someArgName
            )
        );
    }
}
