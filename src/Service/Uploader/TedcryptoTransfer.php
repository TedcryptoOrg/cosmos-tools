<?php

namespace App\Service\Uploader;

use Doctrine\DBAL\Driver\Exception;
use Psr\Log\LoggerInterface;

class TedcryptoTransfer
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function upload(string $directoryPath, string $filename): string
    {
        $this->cleanup($directoryPath, $filename);

        try {
            $tar = new \PharData($directoryPath.$filename.'.tar');
            $tar->buildFromDirectory($directoryPath);
            $tar->compress(\Phar::GZ);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://transfer.tedcrypto.io/');

            curl_setopt($ch, CURLOPT_POST,true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'file' => new \CURLFile($directoryPath.$filename.'.tar.gz'),
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_VERBOSE, 1);

            $downloadUrl = curl_exec($ch);
            if (200 !== curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                throw new \Exception('Error while uploading file');
            }
            curl_close($ch);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }

        $this->cleanup($directoryPath, $filename);

        return $downloadUrl;
    }

    private function cleanup(string $directoryPath, string $filename): void
    {
        if (file_exists($directoryPath.$filename.'.tar')) {
            unlink($directoryPath.$filename.'.tar');
        }
        if (file_exists($directoryPath.$filename.'.tar.gz')) {
            unlink($directoryPath.$filename.'.tar.gz');
        }
    }
}