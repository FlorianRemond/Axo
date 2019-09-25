<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;


class StatService
{

    public function getStats()
    {
        $ip = fopen('last_ip.txt', 'c+');
        $check = fgets($ip);

        $file = fopen('counter.txt', 'c+');
        $count = intval(fgets($file));
        //si l'ip du dernier visiteur est différent on incrémente de 1
        if ($_SERVER['REMOTE_ADDR'] != $check) {
            fclose($ip);

            //w+ = ecrase les données dans le fichier
            $ip = fopen('last_ip.txt', 'w+');

            fputs($ip, $_SERVER['REMOTE_ADDR']);
            $count++;
            fseek($file, 0);
            fputs($file, $count);
        }
        fclose($file);
        fclose($ip);
    }

    public function readStats()
    {
        $filename = "C:\Blog\Axo\public\counter.txt";
        $handle = fopen($filename, 'r');
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        echo $contents;
    }


}