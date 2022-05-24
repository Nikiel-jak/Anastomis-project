<?php
/**
 * Logiciel : MakeFont for HTML2PDF
 * 
 * Outil d'aide � la creation de fonte pour HTML2PDF
 * utilise makefont de Olivier PLATHEY 
 * utilise ttf2pt1 (http://ttf2pt1.sourceforge.net/)
 * Distribu� sous la licence GPL. 
 * 
 * il est IMPORTANT de bien comprendre ce tutorial
 * http://fpdf.org/fr/tutorial/tuto7.htm
 * ainsi que le script ci-dessous avant de l'utiliser
 *
 * @author		Laurent MINGUET <webmaster@spipu.net>
 * @version		1.0 - 13/03/2009
 */

/** DEBUT PARAMETRES **/
$real	= 'arial';
$name	= 'arial';
$types	= array('');
$enc	= 'ISO-8859-2';
$patch	= array();
/** FIN PARAMETRES **/

/****************************************************************
 *************** NE RIEN MODIFIER CI-DESSOUS ********************
 ****************************************************************/

if (isset($_GET['verif']))
{
	$html = '<page orientation="paysage">';
	$html.= '<table style="font-size: 30px; border: solid 1px black; font-family: helvetica">';
	foreach($types as $type)
	{
		$style = '';
		$html.= '<tr><td>Font '.$real.' '.$type.'</td></tr>';
		$html.= '<tr><td style="border: solid 1px #550000; font-family: '.$real.';';
		if ($type=='b' || $type=='bi') $html.= 'font-weight: bold;';
		if ($type=='i' || $type=='bi') $html.= 'font-style: italic;';
		$html.= '">ĄŚĆŻŹąśćżłŁabcdefghijklmnopqrstuvwxyz<br>ABCDEFGHIJKLMNOPQRSTUVWXYZ<br>0123456789</td></tr>';
	}
	$html.= '</table>';
	$html.= '</page>';
	// conversion HTML => PDF
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
	$html2pdf = new HTML2PDF();
	foreach($types as $type) $html2pdf->AddFont($real, strtoupper($type), $name.$type.'.php');
	$html2pdf->WriteHTML($html);
	$html2pdf->Output();
	exit;	
}
?>
<html>
	<head>
		<title>Creation de fonte</title>
	</head>
	<body>
		<pre>
<?php
echo 'Create FPDF font from TTF font : '.$name."\n";
echo 'Read this before using this script : <a href="http://fpdf.org/fr/tutorial/tuto7.htm">http://fpdf.org/fr/tutorial/tuto7.htm</a>'."\n";
require('../_tcpdf_5.0.002/fonts/utils/makefont.php');

// v�rification des fichiers
foreach($types as $type)
{
	if (!is_file($name.$type.'.ttf'))
	{
		echo ' File not found : '.$name.$type.'.ttf'."\n"; exit;	
	}
	else
		echo ' File found : '.$name.$type.'.ttf'."\n";
	
	if (is_file($name.$type.'.afm'))	unlink($name.$type.'.afm');
	if (is_file($name.$type.'.t1a'))	unlink($name.$type.'.t1a');
	if (is_file($name.$type.'.z'))		unlink($name.$type.'.z');
	if (is_file($name.$type.'.php'))	unlink($name.$type.'.php');
}

// conversion au format afm
echo ' Generate AFM file'."\n";
foreach($types as $type)
{
	echo '  '.$name.' '.$type."\n";
	exec('ttf2pt1 -b -L iso-8859-2.map '.$name.$type.'.ttf '.$name.$type, $output);
	
}

// generation du fichier de definition
echo ' Generate PHP file in '.$enc.' with patch : '.print_r($enc, true)."\n";
foreach($types as $type)
{
	echo '  '.$name.' '.$type."\n";
	echo '<div style="border: solid 1px black; margin-left: 15px; width: 600px;">';
	MakeFont($name.$type.'.pfb',$name.$type.'.afm', $enc, $patch);
	echo '</div>';
}

// nettoyage des fichiers inutiles
echo ' Delete and move files'."\n";
foreach($types as $type)
{
	echo '  '.$name.' '.$type."\n";
	if (is_file($name.$type.'.afm'))	unlink($name.$type.'.afm');
	if (is_file($name.$type.'.t1a'))	unlink($name.$type.'.t1a');
	if (is_file('../_tcpdf_5.0.002/fonts/'.$name.$type.'.z'))		unlink('../_tcpdf_5.0.002/fonts/'.$name.$type.'.z');
	if (is_file('../_tcpdf_5.0.002/fonts/'.$name.$type.'.php'))	unlink('../_tcpdf_5.0.002/fonts/'.$name.$type.'.php');
	rename($name.$type.'.z',	'../_tcpdf_5.0.002/fonts/'.$name.$type.'.z');
	rename($name.$type.'.php',	'../_tcpdf_5.0.002/fonts/'.$name.$type.'.php');

}
?>
		</pre>
		<iframe src="./?verif" style="width: 600px; height: 500px"></iframe>
	</body>
</html>