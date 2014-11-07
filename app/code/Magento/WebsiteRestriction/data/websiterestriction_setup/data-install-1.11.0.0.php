<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\WebsiteRestriction\Model\Resource\Setup */

$cmsPages = array(
    array(
        'title' => '503 Service Unavailable',
        'identifier' => 'service-unavailable',
        'content' => '<div class="page-title"><h1>We\'re Offline...</h1></div>
<p>...but only for just a bit. We\'re working to make the Magento Enterprise Demo a better place for you!</p>',
        'is_active' => '1',
        'stores' => array(0),
        'sort_order' => 0
    ),
    array(
        'title' => 'Welcome to our Exclusive Online Store',
        'identifier' => 'private-sales',
        'content' => '<div class="private-sales-index">
<div class="box">
<div class="content">
<h1>Welcome to our Exclusive Online Store</h1>
<p>If you are a registered member, please <a href="{{store url="customer/account/login"}}">log in here</a>.</p>
<p class="description">Magento is the leading hub for exclusive specialty items for all your home, apparel and entertainment needs!</p>
</div>
</div>
</div>',
        'is_active' => '1',
        'stores' => array(0),
        'sort_order' => 0
    )
);

/**
 * Insert default and system pages
 */
foreach ($cmsPages as $data) {
    $this->getPage()->setData($data)->save();
}
