<?php

namespace App\Service;

use GuzzleHttp\Client;

class ChatGptImageService
{
    private string $apiUrl = 'gpt_call_path';

    public function processReceiptContents(array $categories, string $ocrContent): string
    {
        $client = new Client();

        $response = $client->request('POST', $this->apiUrl, [
            'form_params' => ['categories' => json_encode($categories), 'ocr' => $ocrContent]
        ]);

        return $response->getBody()->getContents();
    }

    public function parseContent(array $categories, $content)
    {
        $receiptData = [];

        foreach ($content as $itemKey => $itemValue) {
            $receiptItem = new \stdClass();
            $receiptItem->id = $categories[$itemKey];
            $receiptItem->name = $itemKey;
            $receiptItem->products = $itemValue;

            $receiptData[] = $receiptItem;
        }

        return $receiptData;
    }
}