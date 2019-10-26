<?php

/**
 * @author Josep Vidal
 */

 namespace app\libraries;

 use Yii;

 class JosepDates
 {

   /* Compara dos dates y diu quina es mes gran
   @ $data1 data string
   @ $data2 data string
   retorna 1 si la primera es més gran i 0 si es la segona la gran
   */
   static function esMesGran($data1 = null , $data2 = null){
    if(!$data1) return false;

    /* SI NO TENEN VALOR ES QUE COMPAREM EN AVUI */
    $data1 = date(strtotime($data1));
    $data2 = $data2 ? date(strtotime($data2)) : date_create();
    
    return ((int)$data1 < date_timestamp_get($data2) ? true : false) ;
   }
 }
