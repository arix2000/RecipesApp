<?php

namespace App\Services;

class DocsParser
{
    public function parse(string $filePath): array
    {
        $jsonContent = file_get_contents($filePath);
        $collection = json_decode($jsonContent, true);

        $endpoints = [];

        foreach ($collection['item'] as $item) {
            $endpoints[] = [
                'name' => $item['name'],
                'method' => $item['request']['method'],
                'url' => $item['request']['url']['raw'],
                'headers' => $item['request']['header'] ?? [],
                'body' => $item['request']['body']['raw'] ?? '',
                'response' => $item['response'] ?? []
            ];
        }

        return $endpoints;
    }
}
