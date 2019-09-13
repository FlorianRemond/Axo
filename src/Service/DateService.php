<?php

namespace App\Service;

class DateService{
    public function getDate(){

        $dateC=new \DateTime();
      //  $dateC=new \DateTime('now');
        $dateJour=$dateC->format('Y-m-d');
        dump($dateJour);

        $dateFile="C:\Blog\Axo\public\date.txt";
        if (is_writable($dateFile)) {
            if (!$handle = fopen($dateFile, 'w+')) {
                echo "Impossible d\'ouvrir le fichier($dateFile)";
                exit;
            }
            if (fwrite($handle, $dateJour) === false) {
                echo "Impossible d\'écrire dans le fichier ($dateFile)";
                exit;
            }
            //echo "MAJ Ok du fichier $dateFile";
            fclose($handle);
            }else{
            echo "Le fichier $dateFile n'est pas accessible en écriture";
           }

       $handle =fopen($dateFile,'r');
        $dateCompteur= fread($handle, filesize($dateFile));
       fclose($handle);

       echo $dateCompteur;
       $dateJ= new \Datetime();
       $dateComparee= $dateJ->format('Y-m-d');

        //comparaison date du jour et date du fichier, et remise à zéro du compteur si différente
        if ($dateComparee != $dateCompteur) {
            echo "ok";
            dump($dateJour);
            dump($dateCompteur);

            $fileCount= "C:\Blog\Axo\public\counter.txt";
            $handleCount = fopen($fileCount, 'w+');
            fwrite($handleCount, 0);
            fclose($handleCount);

            $fileIp="C:\Blog\Axo\public\last_ip.txt";
            $handleIp =fopen($fileIp, 'w+');
            fwrite($handleIp,0);
            fclose($handleIp);
        }else{
            echo 'pas ok ';
        }

    }

}
