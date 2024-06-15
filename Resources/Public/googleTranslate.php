<?php

// Funktion zum Übersetzen eines Textes mit Google Cloud Translation API
function translateText($text, $targetLanguage) {
    // Ihr Google Cloud Translation API-Schlüssel
    $apiKey = '';

    // URL für die API-Anfrage
    $url = 'https://translation.googleapis.com/language/translate/v2?key=' . $apiKey;

    // Text in das JSON-Format konvertieren
    $data = array(
        'q' => $text,
        'target' => $targetLanguage
    );
    $data = json_encode($data);

    // cURL-Optionen für die POST-Anfrage
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data
    );

    // cURL-Initialisierung
    $curl = curl_init();
    curl_setopt_array($curl, $options);

    // Anfrage an die API senden
    $response = curl_exec($curl);

    // cURL-Verbindung schließen
    curl_close($curl);

    // API-Antwort decodieren und übersetzten Text extrahieren
    $translatedText = json_decode($response, true)['data']['translations'][0]['translatedText'];

    return $translatedText;
}

// Die Zeichenkette, die übersetzt werden soll

$text = '<p>To display a list of data records in the plugin preview in the TYPO3 backend, use the following procedure.</p>
<h2>Implementation</h2>
<ol> <li>Complete file Classes/Preview/MyPreviewRenderer.php</li> <li>Complete file Resources/Private/Templates/Tccd/BackendPreview/List.html</li> <li>Parts in Configuration/TCA/Overrides/tt_content.php</li> </ol>
<pre>// Backend plugin preview with record list [A]
$GLOBALS[\'TCA\'][\'tt_content\'][\'types\'][\'list\'][\'previewRenderer\'][\'tccdexamples_tccdpluginlist\'] = Machwert\TccdExamples\Preview\MyPreviewRenderer::class;</pre>
<p>2:To display a list of data records in the plugin preview in the TYPO3 backend, use the following procedure.</p>
<h2>Implementation</h2>
<ol> <li>Complete file Classes/Preview/MyPreviewRenderer.php</li> <li>Complete file Resources/Private/Templates/Tccd/BackendPreview/List.html</li> <li>Parts in Configuration/TCA/Overrides/tt_content.php</li> </ol>
<pre>// 2:Backend plugin preview with record list [A]
$GLOBALS[\'TCA\'][\'tt_content\'][\'types\'][\'list\'][\'previewRenderer\'][\'tccdexamples_tccdpluginlist\'] = Machwert\TccdExamples\Preview\MyPreviewRenderer::class;</pre>
';

// Die Zielsprache für die Übersetzung
$targetLanguage = 'de'; // Hier können Sie die Zielsprache ändern

// Regulärer Ausdruck zum Identifizieren von Zeichenfolgen ohne Leerzeichen, die auf bestimmte Dateiendungen enden
$pattern = '/(?<=\s|^)(\/[^\/\s]+\.(php|html|pdf|jpg|xml|gif|jpeg|png|zip))(?=\s|$)/';

// Ersetzen von passenden Zeichenfolgen durch Platzhalter
$text = preg_replace_callback($pattern, function($matches) {
    return str_repeat('*', strlen($matches[0]));
}, $text);

// Aufteilen des Textes in Teile zwischen Tags und innerhalb von <pre> Tags
$parts = preg_split('/(<pre>.*?<\/pre>|\n)/s', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

// Übersetzen von Textteilen außerhalb von <pre> Tags
foreach ($parts as &$part) {
    if (strpos($part, '<pre>') === false && strpos($part, '</pre>') === false) {
        $part = translateText($part, $targetLanguage);
    }
}

// Zusammenführen der Teile und Ausgabe des übersetzten Textes
$translatedText = implode('', $parts);
echo $translatedText;

?>
