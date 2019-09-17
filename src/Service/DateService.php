<?php

namespace App\Service;

class DateService{
    public function getDate(){

        $dateFile1="C:Blog\Axo\public\date.txt";
        $handle =fopen($dateFile1,'r');
        $dateCompteur= fread($handle, filesize($dateFile1));
        fclose($handle);

       $dateJ= new \Datetime();
       $dateComparee= $dateJ->format('Y-m-d');

        //comparaison date du jour et date du fichier, et remise à zéro du compteur si différente
        if ($dateComparee != $dateCompteur) {

            $dateMaj1=new \DateTime();
            $dateMaj=$dateMaj1->format('Y-m-d');
            $dateFile= "C:Blog\Axo\public\date.txt";
            $handleDate =fopen($dateFile,'w+');
            fwrite($handleDate,$dateMaj);
            fclose($handleDate);

            $fileCount= "C:\Blog\Axo\public\counter.txt";
            $handleCount = fopen($fileCount, 'w+');
            fwrite($handleCount, 0);
            fclose($handleCount);

            $fileIp="C:\Blog\Axo\public\last_ip.txt";
            $handleIp =fopen($fileIp, 'w+');
            fwrite($handleIp,0);
            fclose($handleIp);
        }
    }
}
