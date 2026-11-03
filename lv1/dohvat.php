<?php

require_once 'klase.php';

for ($i = 2; $i <= 6; $i++) {

    $url = "https://stup.ferit.hr/index.php/zavrsni-radovi/page/$i/";

    $html = dohvatiStranicu($url);

    if ($html === false) {
        continue;
    }

    $dom = new DOMDocument();

    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    $articles = $xpath->query("//article");

    foreach ($articles as $article) {

        $linkElement = $xpath->query(".//a", $article)->item(0);
        $imageElement = $xpath->query(".//img", $article)->item(0);

        if (!$linkElement) {
            continue;
        }

        $naziv = trim($linkElement->textContent);
        $link = $linkElement->getAttribute("href");

        $oib = NULL;

        if ($imageElement) {
            $src = $imageElement->getAttribute("src");

            preg_match('/(\d{11})/', $src, $matches);

            $oib = $matches[1] ?? NULL;
        }

        $tekst = dohvatiTekstRada($link);

        $podaci = array(
            'naziv' => $naziv,
            'tekst' => $tekst,
            'link' => $link,
            'oib' => $oib
        );

        $rad = new DiplomskiRadovi($pdo);

        $rad->create($podaci);

        echo "<pre>";
        print_r($podaci);
        echo "</pre>";
    }
}

?>