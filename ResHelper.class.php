<?php

class ResHelper
{
    private static $lastException = null;
    private static $lastTimes = null;

    private function __construct()
    {
    }

    private static function loadCSV($filename)
    {
        if (!file_exists($filename)) {
            self::$lastException = new \Exception('File Not found');

            return [];
        }

        $handle = fopen($filename, 'r');
        while ($line = fgetcsv($handle)) {
            $data[] = $line;
        }

        fclose($handle);

        return $data;
    }

    private static function sendData($data, $settings)
    {
        $curl = curl_init($settings['action']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($data)));

        curl_exec($curl);
        if (curl_error($curl)) {

            self::$lastException = new \Exception(curl_error($curl));
            return false;
        } else {
            $info = curl_getinfo($curl);
            self::$lastTimes[] = $info['total_time'];
        }

        return true;
    }

    public static function sendFromCSV($filename, $settings)
    {
        return self::sendData(self::loadCSV($filename), $settings);
    }

    public static function getLastException()
    {
        return self::$lastException;
    }

    public static function getLastTimes()
    {
        return self::$lastTimes;
    }
}
