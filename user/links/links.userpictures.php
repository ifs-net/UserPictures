<?php
// Load language file
modules_get_language();
pnModLangLoad('UserPictures', 'user');
//change the second argument to reflect what should be printed under icon.
user_menu_add_option(pnModURL('UserPictures','user','main'), _USERPICTURESOWNPICTURES,"modules/UserPictures/user/pnimages/userpictures.gif");
?>