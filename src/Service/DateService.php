<?php

namespace App\Service;

class DateService{
    public function GetDate(){
        //Récupération de la date du jour
        $dateNow1 = new \DateTime('now');
       //  $dateNow1= new \DateTime('tomorrow');
        //Mise au format permettant la manipulation
        $dateNow = $dateNow1->format('Y-m-d');
        //ajout de la date dans un fichier
        $date = fopen('date.txt', 'c+');
        fwrite($date, $dateNow);
        fclose($date);

        //ouverture et lecture du fichier
        $dateTest = "C:\Blog\Axo\public\date.txt";
        $read = fopen($dateTest, 'r');
        $dateCompteur = fread($read, filesize($dateTest));


        //Reprise de la date du jour au bon format
        $dateJourCompare = new \DateTime('now');
        $dateJour = $dateJourCompare->format('Y-m-d');

        //comparaison date du jour et date du fichier, et remise à zéro du compteur si différente
        if ($dateCompteur != $dateJour) {

            $fileCount= "C:\Blog\Axo\public\counter.txt";
            $fileCount = fopen($fileCount, 'c+');
            fwrite($fileCount, 0);
            fclose($fileCount);

            $fileDate="C:\Blog\Axo\public\last_ip.txt";
            $fileDate =fopen($fileDate, 'c+');
            fwrite($fileDate,0);
            fclose($fileDate);
        }
    }

}