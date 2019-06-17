<?php
/*error_reporting(E_ALL);
ini_set("display_errors", 1);

require("eedomus.lib.php");*/

//-------------- Parametres
$periphHP = getArg('periphhp');
$periphHC = getArg('periphhc');
$prixHP = getArg('prixhp');
$prixHC = getArg('prixhc');
if (!$prixHP) { $prixHP = 0.1703; }
if (!$prixHC) { $prixHC = 0.1319; }

$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime($today." -1 days"));

/*** récupération de l'historique du périphérique HP ***/
if (!empty($periphHP))
{
    $resultHP = sdk_json_decode(httpQuery('https://api.eedomus.com/get?action=periph.history&periph_id='.$periphHP,'GET'));
    if (!empty($resultHP['body']['history']))
    {
      //on recupere la derniere valeur et la derniere valeur du jour precedent
        $i = 1;
        foreach ($resultHP['body']['history'] as $dataHP)
        {
            $historyHP[$dataHP[1]] = $dataHP[0];
            if ($i == 1) {
              $historyLastHP[] = $dataHP[0];
            } else {
              if (substr($dataHP[1],0,10) == $yesterday) {
                $historyLastHP[] = $dataHP[0];
                break;
              }
            }
            $i++;
        }
    }
//      print_r($historyHP);
//     print_r($historyLastHP);
}
$prixTotalHP = ($historyLastHP[0]-$historyLastHP[1])*$prixHP;
//echo "<p>Prix HP : ".$prixTotalHP." EUR</p>\r\n";

/*** récupération de l'historique du périphérique HC ***/
if (!empty($periphHC))
{
    $resultHC = sdk_json_decode(httpQuery('https://api.eedomus.com/get?action=periph.history&periph_id='.$periphHC,'GET'));
    if (!empty($resultHC['body']['history']))
    {
      //on recupere la derniere valeur et la derniere valeur du jour precedent
        $i = 1;
        foreach ($resultHC['body']['history'] as $dataHC)
        {
            $historyHC[$dataHC[1]] = $dataHC[0];
            if ($i == 1) {
              $historyLastHC[] = $dataHC[0];
            } else {
              if (substr($dataHC[1],0,10) == $yesterday) {
                $historyLastHC[] = $dataHC[0];
                break;
              }
            }
            $i++;
        }
    }
    // print_r($historyHC);
    // print_r($historyLastHC);
}
$prixTotalHC = ($historyLastHC[0]-$historyLastHC[1])*$prixHC;
//echo "<p>Prix HC : ".$prixTotalHC." EUR</p>\r\n";

if ($prixTotalHC >0 && $prixTotalHP >0) {
  $prixTotal = $prixTotalHC+$prixTotalHP;
  //echo "<p>Prix Total : ".$prixTotal." EUR</p>\r\n";
  $eestatus = "<root><prix><total>".$prixTotal."</total><HP>".$prixTotalHP."</HP><HC>".$prixTotalHC."</HC></prix></root>";
  echo $eestatus;
}

?>
