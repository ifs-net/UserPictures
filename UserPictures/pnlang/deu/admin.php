<?php
/**
 * @package      UserPictures
 * @version      $Id$
 * @author       Florian Schie�l
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

// main
define('_USERPICTURESSETASAVATARERROR',			'Es ist ein Fehler beim Speichern eines Avatars aufgetreten');
define('_USERPICTURESFUNCTIONDONE',				'Funktion erfolgreich abgeschlossen');
define('_USERPICTURESACTUALTEMPLATE',			'Aktuell gesetzte Vorlage f�r diese Funktion');
define('_USERPICTURESGOONORSTART',				'Fortsetzen / starten');
define('_USERPICTURESAVATARSETFOR',				'Anzahl der Accounts auf welche Funktion erfolgreich angewendet wurde');
define('_USERPICTURESSELECTTEMPLATEFIRST',		'Um diese Funktion zu nutzen bitte erst eine Vorlage als Grundlage f�r diese Funktion bestimmen');
define('_USERPICTURESOVERWRITEAVATARS',			'Es ist m�glich, im Nachhinein alle Bilder der oben angegebenen Vorlage als Avatar speichern zu lassen und diese Funktion auf alle Nutzer anzuwenden. Eventuell existierende Avatare werden dadurch �berschrieben.');
define('_USERPICTURESOVERWRITEAVATARSNOW',		'Avatare setzen');
define('_USERPICTURESSTORED',					'Einstellungen gespeichert');
define('_USERPICTURESMAINADMINPANEL',			'Administration von UserPictures');
define('_USERPICTURESALLOWUPLOADS',				'Bilder-Uploads zulassen');
define('_USERPICTURESVERIFYTEXT',				'Text der im Upload-Formular angezeigt wird (Kriterien f�r Bilder usw.)');
define('_USERPICTURESSEND',						'�bertragen');
define('_USERPICTURESTEMPLATESADMINMAIN',		'Bilder-Vorlagen');
define('_USERPICTURESADMINMAIN',				'Zentrale Einstellungen');
define('_USERPICTURESTOACTIVETE',				'Bilder aktivieren');
define('_USERPICTURESCONVERT',					'Wo befindet sich das Programm "convert"? (Standard: /usr/bin/convert)');
define('_USERPICTURESDISABLEDTEXT',				'Text der angezeigt wird, falls derzeitig keine Bilder hochgeladen werden d�rfen');
define('_USERPICTURESMAXFILESIZE',				'Maximale Upload-Gr��e in KB (Standard 1500) bevor ein Bild auf die Gr��e skaliert wird, die in der Vorlage festgelegt ist');
define('_USERPICTURESOWNUPLOADS',				'Wie viele Bilder darf ein Benutzer in seine private Galerie hochladen?');
define('_USERPICTURESPICTURES',					'Bilder');
define('_USERPICTURESMAXWIDTHUSERGALLERY',		'Maximale Breite der Bilder in der privaten Benutzer-Galerie');
define('_USERPICTURESMAXHEIGHTUSERGALLERY',		'Maximale H�he der Bilder in der privaten Benutzer-Galerie');
define('_USERPICTURESPIXEL',					'Pixel');
define('_USERPICTURESTHUMBNAILSIZE',            'Gr��e der Vorschau-Bilder');
define('_USERPICTURESAVATARDIRECTORY',			'Avatar-Verzeichnis');
define('_USERPICTURESATTENTIONONCHANGE',        'Bitte beachte, dass die Vorschaubilder gel�scht werden m�ssen, falls die Gr��e sp�ter ge�ndert wird - neue Vorschau-Bilder werden bei Bedarf erzeugt');
define('_USERPICTURESPIXELCROSSPIXEL',          'Format: [Pixel]x[Pixel]. Z.B. 110x110 (bitte "x" benutzen, nicht "*" oder etwas anderes)');
define('_USERPICTURESDATADIR',					'Pfad zum Bilder-Verzeichnis. Dies muss relativ zum Zikula-Basis-Verzeichnis angegeben werden und f�r den Webserver schreibbar sein!');
define('_USERPICTURESALLOWAVATARMANAGEMENT',    'Die Verwaltung von Avataren durch UserPictures erlauben');
define('_USERPICTURESAVATARSIZE',       		'Format eines Avatars in der Zikula-Installation');
define('_USERPICTURESTHUMBNAILCREATIONMETHOD',	'Methode der Vorschau-Erzeugung');
define('_USERPICTURESTCCONVERT',				'"convert" (ImageMagick) per shell_exec');
define('_USERPICTURESTCGDLIB',					'gdlib (sch�rfer, mehr M�glichkeiten)');
define('_USERPICTURESSHOWHINTONTHUMBNAILS',		'Einen Hinweis in den Vorschau-Bildern zeigen (daf�r muss "gdlib" ausgew�hlt sein)');
define('_USERPICTURESDELETETHUMBNAILSFORRECREATION',	'L�schen aller Vorschau-Bilder f�r Neu-Erstellung (das kann dauren!)');
define('_USERPICTURESDELETEDTHUMBNAILS',		'Anzahl gel�schter Vorschau-Bilder');
define('_USERPICTURESTEMPLATETOAVATAR',			'Neue Uploads einer Vorlage automatisch als Avatar eines Benutzers setzen');
define('_USERPICTURESDONOTUSE',					'deaktiviert');

// global category management
define('_USERPICTURESGLOBALCATEGORYMANAGEMENT',	'Verwaltung der allgemeinen Bilder-Kategorien');
define('_USERPICTURESCATEGORIES',				'Allgemeine Kategorien');
define('_USERPICTURESADDGLOBALCATEGORY',		'Allgemeine Bilder-Kategorie erstellen oder bearbeiten');
define('_USERPICTURESGLOBALCATEGORYEXPL',		'Falls eine allgemeine Kategorie erstellt wird, k�nnen alle Benutzer ihre Bilder zu dieser Kategorie hinzuf�gen - damit werden echte Gemeinschafts-Galerien erm�glicht');
define('_USERPICTURESTEXT',						'Beschreibung der allgemeinen Kategorie');
define('_USERPICTURESDATE',						'Zugeordnetes Datum (wird zur Sortierung verwendet)');
define('_USERPICTUREDELETEGLOBALCATEGORY',		'Diese allgemeine Kategorie l�schen');
define('_USERPICTURESSAVEGLOBALCAT',			'Speichern / Aktualisieren');
define('_USERPICTURESCLEARFORM',				'Formular l�schen');
define('_USERPICTURESEXISTINGCATEGORIES',		'Vorhandene allgemeine Kategorien');
define('_USERPICTURESNUMBEROFCATEGORIES',		'Anzahl vorhandener allgemeiner Kategorien');
define('_USERPICTURESACTIONDONE',				'Aktion erfolgreich beendet');

// templates
define('_USERPICTURESIMAGESAVAILABLE',			'vorh. Bilder');
define('_USERPICTURESTEMPLATESADMINPANEL',		'Verwaltung der Vorlagen');
define('_USERPICTURESWHATARETEMPLATES',			'F�r jede Art von Bildern, die ein Benutzer neben seiner privaten Galerie hochladen kann, muss eine eigene Vorlage erstellt werden');
define('_USERPICTURESNEWTEMPLATE',				'Vorlagen-Einstellungen');
define('_USERPICTURESTITLE',					'Titel');
define('_USERPICTURESMAXWIDTH',					'Bilder auf diese Breite skalieren');
define('_USERPICTURESMAXHEIGHT',				'Bilder auf diese H�he skalieren');
define('_USERPICTURESPIXELS',					'Pixel');
define('_USERPICTURESDDEFAULTIMAGE',			'Standard-Bild');
define('_USERPICTURESTOVERIFY',					'Neu hochgeladene Bilder m�ssen vom Administrator kontolliert werden');
define('_USERPICTURESNOTTOVERIFY',				'Neu hochgeladene Bilder m�ssen vom Administrator aktiviert werden');
define('_USERINFOEXISTINGTEMPLATES',			'Verwaltung bestehender Vorlagen');
define('_USERINFOWARNINGFOREDIT',				'Vorsicht beim �ndern dieser Werte: die Gr��e der Bilder sollte nicht ver�ndert werden - oder �ltere Bilder f�hren zu Problemen, da sie nicht automatisch skaliert werden!');
define('_USERPICTURESNOHEIGHT',					'Der Wert f�r die H�he muss eine Zahl sein');
define('_USERPICTURESNOWITDH',					'Der Wert f�r die Breite muss eine Zahl sein');
define('_USERPICTURESNOTITLE',					'Ein Titel f�r die Vorlage ist erforderlich');
define('_USERPICTURESSETTINGSSTORED',			'Einstellungen der Vorlage speichern');
define('_USERPICTURESID',						'ID');
define('_USERPICTURESMAXRESOLUTION',			'maximale Aufl�sung');
define('_USERPICTURESDEFAULTIMAGE',				'Standard-Bild, URL');
define('_USERPICTURESTOVERIFYTABLE',			'muss aktiviert werden');
define('_USERPICTURESNO',						'Nein');
define('_USERPICTURESYES',						'Ja');
define('_USERPICTURESACTION',					'Aktion');
define('_USERPICTURESDELETE',					'L�schen');
define('_USERPICTURESREALLYDELETE',				'Ja, ich will die Vorlage und alle zugeordneten Bilder l�schen');
define('_USERPICTURESERRORSAVING',				'Beim Versuch die Daten zu speichern trat ein Fehler auf');
define('_USERPICTURESEDIT',						'Bearbeiten');
define('_USERPICTURESDELETED',					'Daten wurden gel�scht');
define('_USERPICTURESOWNGALLERY',				'Eigene Galerie (optional)');

// deletepicture
define('_USERPICTURESDELETEPICTURE',			'Ein Bild l�schen');
define('_USERPICTURESPICTUREID',				'Bilder-ID');
define('_USERPICTURESDELETEERROR',				'Beim L�schen des Bildes ist ein Fehler aufgetreten - existiert es wirklich?');

// toactivate
define('_USERPICTURESACTIVATEPICTURE',			'Dieses Bild aktivieren');
define('_USERPICTURESTOACTIVATE',				'Neue Bilder stehen zur Aktivierung bereit');
define('_USERPICTURESACTIVATETEXT',				'Es werden maximal 20 Bilder angezeigt');
define('_USERPICTURESACTIVATED',				'Das Bild wurde aktiviert');

// find orphans
define('_USERPICTURESFINDORPHANS',				'Verwaiste Dateien finden');
define('_USERPICTURESFILESYSTEM',				'Die folgenden Dateien existieren ohne Referenz in der Datenbank - sie k�nnen normalerweise gel�scht werden');
define('_USERPICTURESDELETEFILES',				'Dateisystem bereinigen');
define('_USERPICTURESDBFILESYSTEM',				'Die Datenbank enth�lt Referenzen zu folgenden Dateien, die nicht existieren - die Datenbank-Eintr�ge k�nnen normalerweise gel�scht werden');
define('_USERPICTURESDELETEDBFILES',			'Datenbank bereinigen');
define('_USERPICTURESFILESDELETED',				'Dateisystem wurde bereinigt');
define('_USERPICTURESNOORPHANFILES',			'Dateisystem ist fehlerfrei');
define('_USERPICTURESDBFILESDELETED',			'Datenbank wurde bereinigt');
define('_USERPICTURESNODBORPHANFILES',			'Die Datenbank ist fehlerfrei');
define('_USERPICTURESAMOUNTOFFILES',			'Anzahl Dateien im Bilder-Verzeichnis');
define('_USERPICTURESORPHANPICS',				'Der Besitzer der folgenden Bilder existiert nicht mehr');
define('_USERPICTURESDELETEORPHANPICS',			'Verwaiste Bilder l�schen');
define('_USERPICTURESORPHANPICSDELETED',		'Verwaiste Bilder wurden gel�scht');
define('_USERPICTURESNODBORPHANPICS',			'Keine verwaisten Bilder entdeckt');
?>
