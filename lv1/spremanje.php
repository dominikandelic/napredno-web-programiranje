
<?php

require_once 'klase.php';

// Prolazak kroz stranice od 2 do 6
for ($i = 2; $i <= 6; $i++) {

    $url = "https://stup.ferit.hr/index.php/zavrsni-radovi/page/$i/";

    // Dohvaćanje HTML-a stranice
    $html = dohvatiStranicu($url);

    if ($html === false) {
        continue;
    }

    // Parsiranje HTML-a
    $dom = new DOMDocument();

    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // Pronalazak svih radova na stranici
    $articles = $xpath->query("//article");

    foreach ($articles as $article) {

        $linkElement = $xpath->query(".//a", $article)->item(0);
        $imageElement = $xpath->query(".//img", $article)->item(0);

        if (!$linkElement) {
            continue;
        }

        // Dohvaćanje naziva i linka rada
        $naziv = trim($linkElement->textContent);
        $link = $linkElement->getAttribute("href");

        // Dohvaćanje OIB-a
        $oib = NULL;

        if ($imageElement) {
            $src = $imageElement->getAttribute("src");

            preg_match('/(\d{11})/', $src, $matches);

            $oib = $matches[1] ?? NULL;
        }

        // Dohvaćanje kompletnog teksta rada
        $tekst = dohvatiTekstRada($link);

        // Priprema podataka
        $podaci = array(
            'naziv' => $naziv,
            'tekst' => $tekst,
            'link' => $link,
            'oib' => $oib
        );

        // Kreiranje objekta
        $rad = new DiplomskiRadovi($pdo);

        $rad->create($podaci);

        // Spremanje rada u bazu
        $rad->save();

        echo "Spremljen rad: " . htmlspecialchars($naziv) . "<br>";
    }
}


// Dohvaćanje svih radova iz baze
$rad = new DiplomskiRadovi($pdo);

$sviRadovi = $rad->read();

echo "<h2>Svi radovi spremljeni u bazu:</h2>";

echo "<pre>";
print_r($sviRadovi);
echo "</pre>";

?>
