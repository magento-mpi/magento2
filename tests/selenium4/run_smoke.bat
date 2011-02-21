set SELENIUM_DEBUG_LEVEL=INFO
call runtest.bat tests\Admin\Category\addroot.php
pause
call runtest.bat tests\Admin\Category\addsub.php
pause
call runtest.bat tests\Admin\AttributeSet\AddSet.php
pause
call runtest.bat tests\Admin\ProductAttributes\AddDropDownAttributeOnProductPage.php
pause
call runtest.bat tests\Admin\Scope\Site.php 
pause
call runtest.bat tests\Admin\Scope\Store.php 
pause
call runtest.bat tests\Admin\Scope\StoreView.php
pause
call runtest.bat tests\Admin\Scope\SetUp.php
pause
call runtest.bat tests\Admin\Product\RequiredFields\AddSimpleProduct.php
pause
call runtest.bat tests\Admin\Product\RequiredFields\AddVirtualProduct.php
pause
call runtest.bat tests\Admin\Product\RequiredFields\AddDownloadableProduct.php
pause
call runtest.bat tests\Admin\Product\RequiredFields\AddConfigurableProduct.php
pause
call runtest.bat tests\Admin\Product\RequiredFields\AddGiftCard.php
pause
call runtest.bat tests\Admin\OrderWorkFlow\Order1.php
pause
call runtest.bat tests\Admin\OrderWorkFlow\Order2.php
pause
