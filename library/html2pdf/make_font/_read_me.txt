********************************************************
** This program is distributed under the GPL License, **
** for more information see file _GPL.txt or          **
** http://www.gnu.org/licenses/licenses.fr.html#GPL   **
**                                                    **
**  Copyright 2000-2009 by Laurent Minguet            **
********************************************************
*******************************************
* MakeFont v1.0 for HTML2PDF - 13/03/2009 *
*******************************************

WARNING :
--------
 works only under a WAMP server because of ttf2pt1
 
 really READ and UNDERSTAND the tutorial of how create a font for FPDF
 http://fpdf.org/fr/tutorial/tuto7.htm, and also the script index.php
 before using it.


How to use :
------------
 - The directory of this script must be copied in the directory of HTML2PDF
  
 - Example for the Arial font
    written in normal, bold, italic, bold + italic
    type 'ISO-8859-1',
    correcting the euro symbol
 
     * The names of the 4 ttf files must be lowercase:
        arial.ttf, arialb.ttf, ariali.ttf, arialbi.ttf
    
     * Copy the 4 ttf files in the directory of this script
 
     * Edit the index.php script and specify the following parameters:
        $real  = 'Arial';                   // name of the font
        $name  = 'arial';                   // base name of the ttf files
        $types = array('', 'b', 'i', 'bi'); // the different types of writing
        $enc   = 'ISO-8859-1';              // name of the encoding
        $patch = array (164 => 'Euro');     // correcting Euro symbol

     * Run the index.php script from a web browser
 
     * The font is automatically created and copied in the directory font of FPDF,
       and usable in html2pdf. An example is displayed automatically

change log :
-----------
 see the _lisez_moi.txt file, in french sorry ;)

Help & Support :
---------------
 For questions and bug reports, thank you to use only the support link below.
 I will answer to your questions only on it... 

Informations :
-------------
 Programming in PHP4

 Programmer : Spipu
      email    : webmaster@spipu.net
      web site : http://html2pdf.fr/
      support  : http://html2pdf.fr/forum.php

Thanks :
-------
 * Olivier PLATHEY for his script makefont include in Fpdf (http://www.fpdf.org/)
 * Andrew Weeks for his library ttf2pt1
 * yAronet for hosting support forum