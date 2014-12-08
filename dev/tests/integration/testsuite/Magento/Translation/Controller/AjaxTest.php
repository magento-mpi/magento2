<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Translation\Controller;

class AjaxTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @dataProvider indexActionDataProvider
     */
    public function testIndexAction($postData)
    {
        $this->getRequest()->setPost('translate', $postData);
        $this->dispatch('translation/ajax/index');
        $this->assertEquals('{success:true}', $this->getResponse()->getBody());
    }

    public function indexActionDataProvider()
    {
        return [['test'], [['test']]];
    }
}
