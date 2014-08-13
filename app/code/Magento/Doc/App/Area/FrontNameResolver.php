<?php
/**
 * Documentation area front name resolver. Reads front name from configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\App\Area;

/**
 * Class FrontNameResolver
 * @package Magento\Doc\App\Area
 */
class FrontNameResolver implements \Magento\Framework\App\Area\FrontNameResolverInterface
{
    const PARAM_WIKI_FRONT_NAME = 'doc.frontName';

    /**
     * Documentation area code
     */
    const AREA_CODE = 'doc';

    /**
     * Default area front name
     *
     * @var string
     */
    protected $defaultFrontName;

    /**
     * Constructor
     *
     * @param string $defaultFrontName
     */
    public function __construct($defaultFrontName = null)
    {
        $this->defaultFrontName = $defaultFrontName ? $defaultFrontName : self::AREA_CODE;
    }

    /**
     * Retrieve area front name
     *
     * @return string
     */
    public function getFrontName()
    {
        return $this->defaultFrontName;
    }
}
