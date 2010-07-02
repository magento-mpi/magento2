BUILD_TOOLS="/opt/builds/build"

$BUILD_TOOLS/pavuk -quiet -nredirs 10 -retry 5 -mode dontstore --logfile /dev/null -lmax 5 -dmax 50 -remove_adv -skip_pattern *.jpg,*.gif,*.css,*.ico,*.png -noRobots -adomain kq.varien.com http://kq.varien.com/builds/$1
#$BUILD_TOOLS/pavuk -mode dontstore --logfile $BUILD_TOOLS/pavuk.log -lmax 3 -dmax 30 -remove_adv -skip_pattern *.jpg,*.gif,*.css,*.ico,*.png -noRobots -adomain magentocommerce.com http://magentocommerce.com/asdas

#echo $?
#exit 0
# -mode dontstore 