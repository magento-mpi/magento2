set SELENIUM_DEBUG_LEVEL=DEBUG
call runtest.bat tests\Admin\User\Add.php 
call runtest.bat tests\Admin\AttributeSet\AddSet.php 
call runtest.bat tests\Admin\Category\addroot.php
call runtest.bat tests\Admin\Scope\Site.php 
call runtest.bat tests\Admin\Scope\Store.php 
call runtest.bat tests\Admin\Scope\StoreView.php
call runtest.bat tests\Admin\Category\addsub.php 
call runtest.bat tests\Admin\Product\AddSimple.php
call runtest.bat tests\Admin\Product\DublicateSimple.php 
call runtest.bat tests\Admin\Scope\SetUp.php
call runtest.bat tests\Frontend\Category\Open.php 
call runtest.bat tests\Frontend\Product\Open.php 
call runtest.bat tests\Frontend\Checkout\Guest.php
call runtest.bat tests\Frontend\Checkout\Register.php
call runtest.bat tests\Frontend\Checkout\Login.php
call runtest.bat tests\Frontend\Checkout\MultiShippingRegister.php 
call runtest.bat tests\Frontend\Checkout\MultiShippingLogin.php
pause
pause