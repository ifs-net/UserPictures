<?php

// main
define('_USERPICTURESMAINADMINPANEL',			'Administration of UserPictures');
define('_USERPICTURESALLOWUPLOADS',				'Allow picture uploads');
define('_USERPICTURESVERIFYTEXT',				'Some text that is shown in the upload form (criteria for the pictures etc.');
define('_USERPICTURESSEND',						'Submit');
define('_USERPICTURESBROWSER',					'Browse pictures');
define('_USERPICTURESTEMPLATESADMINMAIN',		'Picture templates');
define('_USERPICTURESADMINMAIN',				'Main settings');
define('_USERPICTURESTOACTIVETE',				'Activate pictures');
define('_USERPICTURESCONVERT',					'Where is the program "convert" found? (standard: /usr/bin/convert)');
define('_USERPICTURESDISABLEDTEXT',				'Some text that sould be displayed if uploads are not allowed for the moment');
define('_USERPICTURESMAXFILESIZE',				'Maximal size of the uploads in KB (standard 1500) before the pictures are resized to the size specified in the template');
define('_USERPICTURESOWNUPLOADS',				'How many pictures should a user be able to upload in his own gallery?');
define('_USERPICTURESPICTURES',					'Bilder');
define('_USERPICTURESMAXWIDTHUSERGALLERY',		'Maximal width for pcitures in the own gallery of a user');
define('_USERPICTURESMAXHEIGHTUSERGALLERY',		'Maximal height for pictures in the own gallery of a user');
define('_USERPICTURESPIXEL',					'Pixels');
define('_USERPICTURESTHUMBNAILSIZE',            'Size of the thumbnails');
define('_USERPICTURESAVATARDIRECTORY',			'Avatar directory');
define('_USERPICTURESATTENTIONONCHANGE',        'Please notive that if you want to change the thumbnail size later you have to delete all thumbnail files manually - the new thumbnails will be generated on demand');
define('_USERPICTURESPIXELCROSSPIXEL',          'Format: PixelsxPixels. Example value: 110x110 (use a x not a * or something else)');
define('_USERPICTURESDATADIR',					'Path to the picture\'s directory. This has to be the relative path seen from the postnuke\'s root folder. This directory has to be writeable by the webserver!');
define('_USERPICTURESALLOWAVATARMANAGEMENT',    'Allow the management of avatars in UserPictures');
define('_USERPICTURESAVATARSIZE',       		'Format of an avatar in your postnuke installation');
define('_USERPICTURESTHUMBNAILCREATIONMETHOD',	'Method how to create thumbnails');
define('_USERPICTURESTCCONVERT',				'convert (ImageMagick) via shell_exec');
define('_USERPICTURESTCGDLIB',					'gdlib (sharper, more features)');
define('_USERPICTURESSHOWHINTONTHUMBNAILS',		'Show a hint on the thumbnails (you have to choose gdlib for that feature)');
define('_USERPICTURESDELETETHUMBNAILSFORRECREATION',	'Delete all existing thumbnails for recreation (this may take a while!)');
define('_USERPICTURESDELETEDTHUMBNAILS',		'Number of deleted thumbnails');
 
// global category management
define('_USERPICTURESGLOBALCATEGORYMANAGEMENT',	'Global picture category management');
define('_USERPICTURESCATEGORIES',				'Global categories');
define('_USERPICTURESADDGLOBALCATEGORY',		'Modify or add global picture category');
define('_USERPICTURESGLOBALCATEGORYEXPL',		'If you create global categories all users can assign their images with these categories and this will make real community photo galleries possible');
define('_USERPICTURESTEXT',						'Description of the global category');
define('_USERPICTURESDATE',						'Assigned date (will be used as sort criteria)');
define('_USERPICTUREDELETEGLOBALCATEGORY',		'Delete this global category');
define('_USERPICTURESSAVEGLOBALCAT',			'save / update');
define('_USERPICTURESCLEARFORM',				'Clear form');
define('_USERPICTURESEXISTINGCATEGORIES',		'Existing global categories');
define('_USERPICTURESNUMBEROFCATEGORIES',		'Number of existing global categories');
define('_USERPICTURESACTIONDONE',				'Action done successfully');

// templates
define('_USERPICTURESTEMPLATESADMINPANEL',		'Management for templates');
define('_USERPICTURESWHATARETEMPLATES',			'For each type of picture a user should be able to upload beside the own gallery you have to create an own template');
define('_USERPICTURESNEWTEMPLATE',				'Create a new template');
define('_USERPICTURESTITLE',					'Title');
define('_USERPICTURESMAXWIDTH',					'Resize pictures to this width');
define('_USERPICTURESMAXHEIGHT',				'Resize pictures to this height');
define('_USERPICTURESPIXELS',					'Pixels');
define('_USERPICTURESDDEFAULTIMAGE',			'Default image');
define('_USERPICTURESTOVERIFY',					'New uploaded pictures have to be verified by the site administrator');
define('_USERPICTURESNOTTOVERIFY',				'New uploaded pictures will not have to be activated by the admin');
define('_USERINFOEXISTINGTEMPLATES',			'Management of the existing templates');
define('_USERINFOWARNINGFOREDIT',				'Attention whenever editing these values: Do not change the size of the pictures - otherwise you might have problems with oldas pictures - old pictures will not be resized after changing the values for a template');
define('_USERPICTURESNOHEIGHT',					'The value for the height has to be numeric');
define('_USERPICTURESNOWITDH',					'The value for the width has to be numeric');
define('_USERPICTURESNOTITLE',					'A title for the template is neccessary');
define('_USERPICTURESSETTINGSSTORED',			'update / store settings for the template');
define('_USERPICTURESID',						'ID');
define('_USERPICTURESMAXRESOLUTION',			'max. resolution');
define('_USERPICTURESDEFAULTIMAGE',				'Default image');
define('_USERPICTURESTOVERIFYTABLE',			'has to be activated');
define('_USERPICTURESNO',						'no');
define('_USERPICTURESYES',						'yes');
define('_USERPICTURESACTION',					'action');
define('_USERPICTURESDELETE',					'delete');
define('_USERPICTURESREALLYDELETE',				'Yes I want to delete the template and all associated images');
define('_USERPICTURESERRORSAVING',				'An error occured while trying to store the data');
define('_USERPICTURESEDIT',						'edit');
define('_USERPICTURESDELETED',					'Data was deleted');
define('_USERPICTURESOWNGALLERY',				'own gallery (optional)');

// browser
define('_USERPICTURESCHOSETEMPLATE',			'Please choose the template of which you want to see the uploaded pictures');
define('_USERPICTURESMANAGE',					'choose');
define('_USERPICTURESUSERGALLERIES',			'User\'s own picture gallery');
define('_USERPICTURESUSERNAME',					'Username');
define('_USERPICTURESCOMMENT',					'Comment');
define('_USERPICTURESDELETETHISPICTURE',		'Delete this image');
define('_USERPICTURESIMAGESAVAILABLE',			'Images uploaded');

// deletepicture
define('_USERPICTURESDELETEPICTURE',			'Delete a picture');
define('_USERPICTURESPICTUREID',				'Picture-ID');
define('_USERPICTURESDELETEERROR',				'An error occured while trying to delete the image. Is the image really existing?');

// toactivate
define('_USERPICTURESACTIVATEPICTURE',			'Activate this picture');
define('_USERPICTURESTOACTIVATE',				'New pictures awaiting your activation');
define('_USERPICTURESACTIVATETEXT',				'20 pictures are shown max.');
define('_USERPICTURESACTIVATED',				'Picture was activated');

// find orphans
define('_USERPICTURESFINDORPHANS',				'Find orphan files');
define('_USERPICTURESFILESYSTEM',				'The following files exist in the file system and there is no database record for the files. Normally these files can be deleted');
define('_USERPICTURESDELETEFILES',				'Clean the file system');
define('_USERPICTURESDBFILESYSTEM',				'The following files are marked as existing but there are no files in the file system. Normally the database entries for these files can be deleted');
define('_USERPICTURESDELETEDBFILES',			'Clean database');
define('_USERPICTURESFILESDELETED',				'File system cleaned');
define('_USERPICTURESNOORPHANFILES',			'File system has no errors!');
define('_USERPICTURESDBFILESDELETED',			'Database was cleaned');
define('_USERPICTURESNODBORPHANFILES',			'Database has no errors');
define('_USERPICTURESAMOUNTOFFILES',			'Number of files in the picture directory');
define('_USERPICTURESORPHANPICS',				'The owner of the following pictures does not exist any more');
define('_USERPICTURESDELETEORPHANPICS',			'Delete the orphan pictures');
define('_USERPICTURESORPHANPICSDELETED',		'Orphan pictures are deleted now');
define('_USERPICTURESNODBORPHANPICS',			'No orphan pictures found');
?>