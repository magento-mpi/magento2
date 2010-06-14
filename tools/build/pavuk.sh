SCRIPTS_PATH="/opt/builds/script/"


echo -n > $SCRIPTS_PATH/pavuk.log
$SCRIPTS_PATH/pavuk -quiet -nredirs 10 -retry 5 -mode dontstore --logfile $SCRIPTS_PATH/pavuk.log -lmax 5 -dmax 50 -remove_adv -skip_pattern *.jpg,*.gif,*.css,*.ico,*.png -noRobots -adomain kq.varien.com http://kq.varien.com/builds/$1
#$SCRIPTS_PATH/pavuk -mode dontstore --logfile $SCRIPTS_PATH/pavuk.log -lmax 3 -dmax 30 -remove_adv -skip_pattern *.jpg,*.gif,*.css,*.ico,*.png -noRobots -adomain magentocommerce.com http://magentocommerce.com/asdas

#echo $?
#exit 0
# -mode dontstore 