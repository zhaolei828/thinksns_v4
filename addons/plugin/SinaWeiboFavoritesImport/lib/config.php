<?php
header('Content-Type: text/html; charset=UTF-8');

define( "WB_AKEY" , '4154048779' );
define( "WB_SKEY" , '2f51b6c6a1fdd2df45c3fdd4a9c0e09f' );
//define( "WB_CALLBACK_URL" , 'http://6jlife.com/callback' );
//define( "WB_CALLBACK_URL" , Addons::adminPage('importFavoritesWeibo') );
define( "WB_CALLBACK_URL" , Addons::createAddonShow('SinaWeiboFavoritesImport','importFavoritesWeibo'));
