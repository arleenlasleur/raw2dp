<?php
if('cli'!==PHP_SAPI) return;
echo 'Raw2Directport   IRRemote raw dump to microsecond delays converter v. 0.1   (c) Arleen Lasleur 2020'.PHP_EOL;

if(!isset($argv[1]) || !isset($argv[2])){
  echo 'Call: php raw2dp file.raw file.ino'.PHP_EOL;
  echo 'File format: last dump string contents after colon (:)'.PHP_EOL;
  echo 'Example: 23350 9000 -4000 550 -600 550 -550 etc.'.PHP_EOL;
  return;
}

$outfile=fopen($argv[2],"w");
if(!$outfile){
  echo 'Target file is inacessible.';
  return;
}
$infile=fopen($argv[1],"r");
$cmd=1;

fputs($outfile,'void proc_mark(long usec){'.PHP_EOL);
fputs($outfile,'  cli();'.PHP_EOL);
fputs($outfile,'  while(usec>0){'.PHP_EOL);
fputs($outfile,'    digitalWrite(3,1); delayMicroseconds(10);'.PHP_EOL);
fputs($outfile,'    digitalWrite(3,0); delayMicroseconds(10);'.PHP_EOL);
fputs($outfile,'    usec-=26;'.PHP_EOL);
fputs($outfile,'  }'.PHP_EOL);
fputs($outfile,'  sei();'.PHP_EOL);
fputs($outfile,'}'.PHP_EOL);

if($infile){
  echo 'Working... ';
  while (($line=fgets($infile)) !== false) {
     ir_convert($line,$cmd,$outfile); $cmd++;
  }
  fclose($infile);
  fclose($outfile);
  echo 'done.'.PHP_EOL;
}else echo 'Source file is inacessible or does not exist.';

function ir_convert($str,$cmd,$outfile){
  $arr=explode(" ",$str);
  unset($arr[0]);                                 // ignore first number
  fputs($outfile,'void proc_send_'.$cmd.'(){'.PHP_EOL);
  foreach($arr as $ac) if(strlen($ac)>0){
    $wstr='';
    $x=intval($ac);
    if($x>=0) $wstr.='  proc_mark('.$x.');';               // IR LED blinking
     else $wstr.='delayMicroseconds('.-1*$x.');'.PHP_EOL;  // IR LED off
    fputs($outfile,$wstr);
  }
  fputs($outfile,PHP_EOL.'}'.PHP_EOL);
}
?>