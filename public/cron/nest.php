<?php
require dirname(__FILE__).'/../../application/Environment.php';
$application->bootstrap(array(
     'autoloader', 'config', 'db', 'smtp', 'view' , 'routing','log'
));
$dir = ROOT_PATH.'/var/imports/';       

$newstamp = 0;
$newname = "";
$dc = opendir($dir);
while ($fn = readdir($dc)) {
  $timedat = filemtime("$dir/$fn");
  var_dump("$dir/$fn");
  echo $fn;
  echo $newstamp;
  die();
  if ($timedat > $newstamp) {
    $newstamp = $timedat;
    $newname = $fn;
  }
}
echo $newname;
echo '<br/>';
echo date('Y-m-d H:i:s',strtotime($newstamp));