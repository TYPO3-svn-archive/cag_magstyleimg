.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


ChangeLog
---------

+----------------+---------------------------------------------------------------+
| Version        | Changes                                                       |
+================+===============================================================+
| 2.0.0          | - First version for TYPO3 6.0 and above                       |
|                |                                                               |
|                | - Added FAL compatibility                                     |
|                |                                                               |
|                | - ReST based manual                                           |
|                |                                                               |
|                | - Thumbnails in page module via hook instead of old XCLASS    |
|                |                                                               |
|                | - Some code refactoring                                       |
+----------------+---------------------------------------------------------------+
| 1.0.0          | - Stable version. Last version for TYPO3 4.5 - 4.7            |
|                |                                                               |
|                | - BUGFIX: Display of CE in TYPO3 4.2.x was broken due to      |
|                |   divider2tabs being activated as default (Thx. Matthew K.    |
|                |   for the bugreport)                                          |
|                |                                                               |
|                | - INFO: Better label for the magazine images dimensions       |
|                |   palette (Thx. Matthew K. for the idea)                      |
|                |                                                               |
|                | - INFO: Revised manual. Added section on how to use it with   |
|                |   perfectlightbox.                                            |
+----------------+---------------------------------------------------------------+
| 0.1.3          | - Support for jquery\_thickbox and pmk\_slimbox               |
|                |                                                               |
|                | - New property .layoutWraps. The inner HTML of the magazine   |
|                |   image blocks is now completely configurable via TS          |
|                |                                                               |
|                | - New global register: MAG\_IMG\_CURRENT. This provides all   |
|                |   necessary image information for access via TS               |
+----------------+---------------------------------------------------------------+
| 0.1.2          | - Fixed the bug that captions/links/alttexts sometimes got    |
|                |   rendered with the wrong image                               |
|                |                                                               |
|                | - Fixed the display errors that sometimes occurred in IE6/7   |
|                |   with advanced imageblock layouts                            |
+----------------+---------------------------------------------------------------+
| 0.1.1          | - Updated manual and refactoring                              |
|                |                                                               |
|                | - Support for kj\_imagelightbox2 (1.4.1+)                     |
|                |                                                               |
|                | - Support for dam\_ttcontent (1.0.1+)                         |
|                |                                                               |
|                | - Fixed caption alignment                                     |
|                |                                                               |
|                | - Tested with different X(HTML) Doctypes                      |
+----------------+---------------------------------------------------------------+
| 0.1.0          | - Beta version including all features                         |
+----------------+---------------------------------------------------------------+
| 0.0.9          | Non public version                                            |
|                |                                                               |
|                | - Code cleanup                                                |
|                |                                                               |
|                | - Added support for TYPO3 versions below 4.0                  |
+----------------+---------------------------------------------------------------+
| 0.0.8          | Non public version                                            |
|                |                                                               |
|                | - Updated manual                                              |
|                |                                                               |
|                | - Implemented textmargin property when used as CE             |
|                |                                                               |
|                | - Implemented blockWrap property                              |
+----------------+---------------------------------------------------------------+
| 0.0.7          | Non public version                                            |
|                |                                                               |
|                | - Implemented positioning and bodytext field. Can now be used |
|                |   like the text/w image element                               |
|                |                                                               |
|                | - Added icons for Web>Page and Web>List                       |
|                |                                                               |
|                | - Image compression now possible                              |
|                |                                                               |
|                | - BUGFIX: When no imgs were selected, this resulted in an     |
|                |   mysql error (Thx. Tilman Schlereth)                         |
|                |                                                               |
|                | - Added clarification to language files for CEW that there is |
|                |   only support for up to eight images (Thx. Tilman Schlereth) |
+----------------+---------------------------------------------------------------+
| 0.0.6          | Non public version                                            |
|                |                                                               |
|                | - Integration of flexform field for config settings           |
|                |                                                               |
|                | - Padding between images can now be set on each CE            |
|                |                                                               |
|                | - Borders around images AND imageblock can now be set on CE   |
|                |                                                               |
|                | - Background can now be set for imageblock on CE              |
|                |                                                               |
|                | - Included preview images for CE in Templavoila by hook       |
+----------------+---------------------------------------------------------------+
| 0.0.5          | - First public alpha release                                  |
+----------------+---------------------------------------------------------------+