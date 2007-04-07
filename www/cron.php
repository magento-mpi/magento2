<?php

require '../app/Mage.php';
Mage::init();
Mage_Cron_Shell::run();
    
