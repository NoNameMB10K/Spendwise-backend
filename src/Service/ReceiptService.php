<?php

namespace App\Service;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ReceiptService
{
    private string $apiUrl = 'ocr_call_path';

    public function processReceiptImage(UploadedFile $image)
    {
        $client = new Client();

        $response = $client->request('POST', $this->apiUrl, [
            'multipart' => [
                [
                    'name' => 'image',
                    'contents' => fopen($image->getPathname(), 'r'),
                    'filename' => $image->getFilename(),
                ],
            ],
        ]);

        return $response->getBody()->getContents();
    }
}