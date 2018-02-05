MageProfis_ImageQueue
===================

Info
-----------
ImageQueue to Compress Images in your Store (like Product Images).

The Compression will be done with a CronTab, JPEG and PNG will be processed in separated Cronjobs

You can choose which compressors you like to use.

You can also set the priotity of some Images.

Added Support for webp, be careful when you are enable this option,
so you should add MageProfis_ImageQueue_Helper_Data::canUseWebp() to your block cache key


Requirements
------------
- PHP >= 5.3.0
- [mozjpeg](https://github.com/mozilla/mozjpeg) or [libjpeg-turbo-tools](https://github.com/libjpeg-turbo/libjpeg-turbo)
- [jpegoptim](https://github.com/tjko/jpegoptim)
- [guetzli](https://github.com/google/guetzli)
- [optipng](http://optipng.sourceforge.net/)
- [pngquant](https://github.com/pornel/pngquant)
- [webp](https://developers.google.com/speed/webp/)

Compatibility
-------------
- Magento >= 1.7.0.2 (up to 1.9.x)
- [Netzarbeiter_NicerImageNames](https://github.com/Vinai/nicer-image-names) (Look at the file [MageProfis_ImageQueue_Netzarbeiter_NicerImageNames.xml](https://github.com/mageprofis/MageProfis_ImageQueue/blob/master/MageProfis_ImageQueue_Netzarbeiter_NicerImageNames.xml)) - not presened in modman file
- [Technooze_Timage](https://github.com/dbashyal/Magento-resize-category-images)

Support
-------
If you encounter any problems or bugs, please create an issue on [GitHub](https://github.com/mageprofis/MageProfis_ImageQueue/issues).

Contribution
------------
Any contribution to the development is highly welcome. The best possibility to provide any code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
* Mathis Klooss

Licence
-------
[Open Software License (OSL-3)](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2017 Loewenstark Digital Solutions GmbH