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
define('_USERPICTURESMAINADMINPANEL',	'Administrationspanel UserPictures');
define('_USERPICTURESALLOWUPLOADS',	'Hochladen von Bildern erlauben');
define('_USERPICTURESVERIFYTEXT',	'Text, welcher beinhaltet, welche Kriterien hochgeladene Bilder erf�llen m�ssen.');
define('_USERPICTURESSEND',		'Abschicken');
define('_USERPICTURESBROWSER',		'Bilderbrowser');
define('_USERPICTURESTEMPLATESADMINMAIN',	'Bildertemplates');
define('_USERPICTURESADMINMAIN',	'Grundeinstellungen');
define('_USERPICTURESTOACTIVETE',	'Freizuschaltende Bilder');
define('_USERPICTURESCONVERT',		'Angabe zum Programm convert (normal: /usr/bin/convert)');
define('_USERPICTURESDISABLEDTEXT',	'Text, welcher angezeigt werden soll, wenn durch den Betreiber dieser Seite das Hochladen von Bildern deaktiviert wurde');
define('_USERPICTURESMAXFILESIZE',	'Maximale Gr��e hochgeladener Dateien in KB (Standard 1500) bevor diese verkleinert werden. Nach dem automatischen Verkleinern der Aufl�sung durch UserPictures sind diese nat�rlich auch in der Dateigr��e kleiner.');
define('_USERPICTURESOWNUPLOADS',	'Wieviel Bilder sollen Communitynutzer zus�tzlich zu den fest per Template eingestellten hochladen d�rfen f�r eine eigene Galerie? (0=keine)');
define('_USERPICTURESAVATARDIRECTORY',			'Avatar-Verzeichnis');
define('_USERPICTURESPICTURES',		'Bilder');
define('_USERPICTURESMAXWIDTHUSERGALLERY',	'Maximale Bilderbreite f�r Benutzergalerien');
define('_USERPICTURESMAXHEIGHTUSERGALLERY',	'Maximale Bilderh�he f�r Benutzergalerien');
define('_USERPICTURESPIXEL',		'Pixel');
define('_USERPICTURESTHUMBNAILSIZE',            'Gr��e der Thumbnails');
define('_USERPICTURESATTENTIONONCHANGE',        'Bitte beachten: Wenn der Wert im Nachhinein ge�ndert wird m�ssen alle alten Thumbailfiles manuell gel�scht werden! Die Dateien werden dann automatisch neu generiert. Das L�schen selbst ist kein Problem.');
define('_USERPICTURESPIXELCROSSPIXEL',          'Angabe: PixelxPixel. Beispielwert: 110x110');
define('_USERPICTURESDATADIR',		'Pfad (mit Schr�gstrich zwingend am Ende) zum Bilderverzeichnis. Das Verzeichnis muss vom Benutzer des Webservers beschreibbar sein! Standardwert: modules/UserPictures/data');
define('_USERPICTURESALLOWAVATARMANAGEMENT',	'Erlaube es, hochgeladene Bilder als pers. Avatar zu kopieren');
define('_USERPICTURESAVATARSIZE',	'Gr��e des zu erstellenden Avatars');

// global category management
define('_USERPICTURESGLOBALCATEGORYMANAGEMENT',	'Globale Bilderkategorien verwalten');
define('_USERPICTURESCATEGORIES',				'Globale Kategorien');
define('_USERPICTURESADDGLOBALCATEGORY',		'�ndern oder Erstellen einer globalen Kategorie');
define('_USERPICTURESGLOBALCATEGORYEXPL',		'Globale Bilderkategorien machen es jedem Benutzer m�glich, seine Bilder einer globalen Bilderkategorie zuzuordnen. So ist es m�glich, dass richtige Community-Fotoalben entstehen k�nnen.');
define('_USERPICTURESTEXT',						'Beschreibung');
define('_USERPICTURESDATE',						'Zugewiesenes Datum (zugleich Sortierkriterium)');
define('_USERPICTUREDELETEGLOBALCATEGORY',		'Kategorie l�schen');
define('_USERPICTURESSAVEGLOBALCAT',			'speichern / aktualisieren');
define('_USERPICTURESCLEARFORM',				'Formular l�schen');
define('_USERPICTURESEXISTINGCATEGORIES',		'Bestehende globale Bilderkategorien');
define('_USERPICTURESNUMBEROFCATEGORIES',		'Anzahl angelegter globaler Kategorien');
define('_USERPICTURESACTIONDONE',				'Aktion erfolgreich ausgef�hrt');

// templates
define('_USERPICTURESTEMPLATESADMINPANEL',	'Verwaltung der Templates');
define('_USERPICTURESWHATARETEMPLATES',	'Je unterschiedliches Bild das ein Benutzer hochladen k�nnen soll muss ein Template existieren.');
define('_USERPICTURESNEWTEMPLATE',	'Neues Template anlegen');
define('_USERPICTURESTITLE',		'Titel');
define('_USERPICTURESMAXWIDTH',		'Gro�e Bilder verkleinern auf Breite');
define('_USERPICTURESMAXHEIGHT',	'Gro�e Bilder verkleinern auf H�he');
define('_USERPICTURESPIXELS',		'Pixel');
define('_USERPICTURESDDEFAULTIMAGE',	'Default-Image');
define('_USERPICTURESTOVERIFY',		'Freischaltung vom Administrator n�tig bei neu hochgeladenen Bildern');
define('_USERPICTURESNOTTOVERIFY',	'Freischaltung vom Administrator nicht n�tig bei neu hochgeladenen Bildern');
define('_USERINFOEXISTINGTEMPLATES',	'Verwaltung / Anzeige angelegter Templates');
define('_USERINFOWARNINGFOREDIT',	'Achtung beim Editieren - Maximale Bildergr��en sollten nicht ge�ndert werden sonst gibt es Probleme mit evtl. bereits hochgeladenen Bildern.');
define('_USERPICTURESNOHEIGHT',		'Bitte eine numerische maximale H�henangabe eingeben');
define('_USERPICTURESNOWITDH',		'Bitte eine numerische maximale Breitenangabe eingeben');
define('_USERPICTURESNOTITLE',		'Bitte einen Titel eingeben f�r das Template');
define('_USERPICTURESSETTINGSSTORED',	'Template-Einstellungen gespeichert / aktualisiert');
define('_USERPICTURESID',		'ID');
define('_USERPICTURESMAXRESOLUTION',	'Max. Aufl�sung');
define('_USERPICTURESDEFAULTIMAGE',	'Default-Image');
define('_USERPICTURESTOVERIFYTABLE',	'Freischaltung nach Upload');
define('_USERPICTURESNO',		'Nein');
define('_USERPICTURESYES',		'Ja');
define('_USERPICTURESACTION',		'Aktion');
define('_USERPICTURESDELETE',		'l�schen');
define('_USERPICTURESREALLYDELETE',	'L�schaktion unwiderruflich ausf�hren: alle Bilder die diesem Template zugeordnet sind werden mit gel�scht!');
define('_USERPICTURESERRORSAVING',	'Fehler beim Speichern des Datensatzes');
define('_USERPICTURESEDIT',		'editieren');
define('_USERPICTURESDELETED',		'L�schaktion erfolgreich');
define('_USERPICTURESOWNGALLERY',	'eigene Galerie (optional)');
define('_USERPICTURESTHUMBNAILCREATIONMETHOD',	'Methode zur Erstellung der kleinen Thumbnailbilder');
define('_USERPICTURESTCGDLIB',		'gdlib (erweiterte Features)');
define('_USERPICTURESTCCONVERT',	'convert (ImageMagick) via shell_exec');
define('_USERPICTURESSHOWHINTONTHUMBNAILS',	'Thumbnailbilder mit Lupe versehen (nur bei gdlib)');
define('_USERPICTURESDELETETHUMBNAILSFORRECREATION',	'Alle existierenden Thumbnails entfernen (Neuerstellung funktioniert automatisch aber dauert etwas, je nachdem wie gross die Galerien sind)');
define('_USERPICTURESDELETEDTHUMBNAILS','Thumbnails wurden entfernt. Anzahl');
define('_',	'');

// browser
define('_USERPICTURESCHOSETEMPLATE',	'Bitte ausw�hlen f�r welches Template alle gespeicherten Bilder angezeigt werden sollen');
define('_USERPICTURESMANAGE',		'ausw�hlen');
define('_USERPICTURESUSERGALLERIES',	'Frei ohne Template hochgeladene Bilder f�r die Benutzergalerie');
define('_USERPICTURESUSERNAME',		'Benutzername');
define('_USERPICTURESCOMMENT',		'Kommentar zum Bild');
define('_USERPICTURESDELETETHISPICTURE','Dieses Bild l�schen');
define('_USERPICTURESIMAGESAVAILABLE',	'Bilder hochgeladen');

// deletepicture
define('_USERPICTURESDELETEPICTURE',	'Benutzerbild l�schen');
define('_USERPICTURESPICTUREID',	'ID des zu l�schenden Bildes');
define('_USERPICTURESDELETEERROR',	'Fehler beim L�schen - eventuell existiert das zu l�schende Bild gar nicht?');

// toactivate
define('_USERPICTURESACTIVATEPICTURE',	'Dieses Bild freischalten');
define('_USERPICTURESTOACTIVATE',	'Freizuschaltende Bilder');
define('_USERPICTURESACTIVATETEXT',	'Es werden maximal 20 Bilder angezeigt.');
define('_USERPICTURESACTIVATED',	'Das Bild wurde freigeschalten und ist nun aktiviert');

// find orphans
define('_USERPICTURESFINDORPHANS',	'Verwaiste Datens�tze finden');
define('_USERPICTURESFILESYSTEM',	'Folgende Dateien sind im Filesystem vorhanden aber nicht in der Datenbank und k�nnen entfernt werden.');
define('_USERPICTURESDELETEFILES',	'Dateisystem bereinigen');
define('_USERPICTURESDBFILESYSTEM',	'Folgende Dateien sind in der Datenbank als existent vermerkt aber existieren nicht im Dateisystem.');
define('_USERPICTURESDELETEDBFILES',	'Datenbank bereinigen');
define('_USERPICTURESFILESDELETED',	'Dateisystem bereinigt');
define('_USERPICTURESNOORPHANFILES',	'Dateisystem hat keine Fehler!');
define('_USERPICTURESDBFILESDELETED',	'Datenbank bereinigt');
define('_USERPICTURESNODBORPHANFILES',	'Datenbank hat keine Fehler!');
define('_USERPICTURESAMOUNTOFFILES',	'Anzahl der Dateien im Datenordner');
define('_USERPICTURESORPHANPICS',	'Folgende Bilder haben keinen Besitzer mehr und koennen geloescht werden');
define('_USERPICTURESDELETEORPHANPICS',	'Bilder vom System entfernen');
define('_USERPICTURESORPHANPICSDELETED','Verwaiste Bilder wurden vom System entfernt');
define('_USERPICTURESNODBORPHANPICS',	'Keine verwaisten Bilder gefunden');
?>