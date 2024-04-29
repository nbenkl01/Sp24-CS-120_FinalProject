<?php
include '../../config.php';

header('Content-Type: application/json');

if (isset($_GET['q']) && isset($_GET['n'])) {
    $bookName = urlencode($_GET['q']);
    $numResults = intval($_GET['n']);
    $apiKey = GOOGLE_API_KEY;
    $timestamp = time(); // current timestamp as cache buster
    $url = "https://www.googleapis.com/books/v1/volumes?q=$bookName&maxResults=$numResults&key=$apiKey&timestamp=$timestamp";

    $response = file_get_contents($url);
    if ($response) {
        $data = json_decode($response, true);
        $items = $data['items'] ?? [];
        $result = [];

        foreach (array_slice($items, 0, $numResults) as $item) {
            $volumeInfo = $item['volumeInfo'] ?? [];
            $saleInfo = $item['saleInfo'] ?? [];
            $isbn = '';
            foreach ($volumeInfo['industryIdentifiers'] ?? [] as $identifier) {
                if ($identifier['type'] === 'ISBN_10' || $identifier['type'] === 'ISBN_13') {
                    $isbn = $identifier['identifier'];
                    break;
                }
            }

            // Default to 10 if no specific price
            $usdPrice = 10;
            // Check for retailPrice first
            if (isset($saleInfo['retailPrice']) && $saleInfo['retailPrice']['currencyCode'] === 'USD') {
                $usdPrice = $saleInfo['retailPrice']['amount'];
            } elseif (isset($saleInfo['listPrice']) && $saleInfo['listPrice']['currencyCode'] === 'USD') {
                $usdPrice = $saleInfo['listPrice']['amount'];
            }

            $result[] = [
                'title' => $volumeInfo['title'] ?? 'No title available',
                'authors' => $volumeInfo['authors'] ?? ['No authors listed'],
                'publisher' => $volumeInfo['publisher'] ?? 'No publisher listed',
                'description' => $volumeInfo['description'] ?? 'No description available',
                'ISBN' => $isbn,
                'smallThumbnail' => $volumeInfo['imageLinks']['smallThumbnail'] ?? 'No image available',
                'usdPrice' => $usdPrice
            ];
        }

        $output = [
            'data_amount' => count($result),
            'books' => $result
        ];

        echo json_encode($output);
    } else {
        echo json_encode(['error' => 'Failed to retrieve data from Google Books API']);
    }
} else {
    echo json_encode(['error' => 'Missing required parameters']);
}
?>
