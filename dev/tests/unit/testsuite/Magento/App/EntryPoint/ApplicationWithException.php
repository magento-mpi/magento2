<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\EntryPoint;

class ApplicationWithException
{
    /**
     * @throws \Exception
     */
    public function execute()
    {
        throw new  \Exception();
    }

}
