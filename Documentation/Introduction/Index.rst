.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Introduction
------------

What does it do?
^^^^^^^^^^^^^^^^

This extension integrates the awesome "Automatic Magazine Layout" PHP
script by  **Harvey Kane** published on  **"A List Apart"** (
`http://www.alistapart.com/articles/magazinelayout
<http://www.alistapart.com/articles/magazinelayout>`_ ) into TYPO3:

"A Magazine-like layout arranges the images at different sizes so that
all images fit within a defined 'square box'. This can be an
attractive way of arranging images when you are dealing with user-
uploaded images, or don't have a graphic designer handy to arrange and
resize them in photoshop."

The extension provides a new content element "Magazine Images" which is
similar in function to the standard text /w image element but makes use
of Kanes rendering engine.

Screenshots
^^^^^^^^^^^

**Example 1:** Two landscape images, one portrait.

.. figure:: ../Images/manual_html_m1079f068.png

**Example 2:** Three landscape images, one portrait.

.. figure:: ../Images/manual_html_m24945207.png

Feature list
^^^^^^^^^^^^

- 6 layout patterns, 44 automatic layout combinations
- Images are scaled using ImageMagick / GraphicsMagick.
- No “on the fly” images like in the original script.
- Specification of alt and title attributes for the all images (split
  option).
- Click enlargement.
- Link wraps with split option.
- Caption support.
- Set imagecompression and type of images generated (gif, jpg, png
  etc.).
- Easy flexform configuration for each magazine imageblock (padding,
  borders, background).
- Positioning w/ text.
- You can use the plugin for rendering any list of images coming from a
  field in DB.
- stdWrap for all images and magazine imageblocks

Please rate
^^^^^^^^^^^

If you can spare a minute please rate the extension in TER. Each good
rating motivates me to keep going. Each bad rating motivates me to
make it better :)

Credits
^^^^^^^

Big thanks to  **Harvey Kane** ( `http://www.ragepank.com
<http://www.ragepank.com/>`_ ) for doing such marvelous algebra and
for publishing his work with excellent explanations.