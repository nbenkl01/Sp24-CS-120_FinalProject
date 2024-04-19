<?php
include '../../config.php';

header('Content-Type: application/json');

if (isset($_GET['q']) && isset($_GET['n'])) {
    $bookName = urlencode($_GET['q']);
    $numResults = intval($_GET['n']);
    $apiKey = GOOGLE_API_KEY;
    $timestamp = time(); // current timestamp as cache buster
    $url = "https://www.googleapis.com/books/v1/volumes?q=$bookName&maxResults=$numResults&key=$apiKey&timestamp=$timestamp";
    // $url = "https://www.googleapis.com/books/v1/volumes?q=$bookName&maxResults=$numResults&key=$apiKey";

    $response = file_get_contents($url);
    if ($response) {
        $data = json_decode($response, true);
        $items = $data['items'] ?? [];
        $result = [];

        foreach (array_slice($items, 0, $numResults) as $item) {
            $volumeInfo = $item['volumeInfo'] ?? [];
            $industryIdentifiers = $volumeInfo['industryIdentifiers'] ?? [];

            $isbn = '';
            foreach ($industryIdentifiers as $identifier) {
                if ($identifier['type'] === 'ISBN_10') {
                    $isbn = $identifier['identifier'];
                    break;
                }
            }

            $result[] = [
                'title' => $volumeInfo['title'] ?? 'No title available',
                'authors' => $volumeInfo['authors'] ?? ['No authors listed'],
                'publisher' => $volumeInfo['publisher'] ?? 'No publisher listed',
                'description' => $volumeInfo['description'] ?? 'No description available',
                'ISBN' => $isbn,
                'smallThumbnail' => $volumeInfo['imageLinks']['smallThumbnail'] ?? 'No image available'
            ];
        }

        // include the data_amount field in the JSON output
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
