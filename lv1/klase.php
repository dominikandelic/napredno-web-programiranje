<?php

$pdo = new PDO(
    "mysql:host=localhost;dbname=radovi;charset=utf8mb4",
    "root",
    ""
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


interface iRadovi {
    public function read();
    public function create($data);
    public function save();
}


class DiplomskiRadovi {

    private $_id = NULL;
    private $_nazivRada = NULL;
    private $_tekstRada = NULL;
    private $_linkRada = NULL;
    private $_oibTvrtke = NULL;

    private $_pdo;

    function __construct($pdo, $data = NULL) {
        $this->_pdo = $pdo;

        if ($data != NULL) {
            $this->create($data);
        }
    }

    function create($data) {
        $this->_nazivRada = $data['naziv'];
        $this->_tekstRada = $data['tekst'];
        $this->_linkRada = $data['link'];
        $this->_oibTvrtke = $data['oib'];
    }

    function save() {

        $sql = "INSERT INTO diplomski_radovi
                (nazivRada, tekstRada, linkRada, oibTvrtke)
                VALUES (:naziv, :tekst, :link, :oib)";

        $stmt = $this->_pdo->prepare($sql);

        $stmt->execute(array(
            ':naziv' => $this->_nazivRada,
            ':tekst' => $this->_tekstRada,
            ':link' => $this->_linkRada,
            ':oib' => $this->_oibTvrtke
        ));

        $this->_id = $this->_pdo->lastInsertId();
    }

    function read() {

        $sql = "SELECT * FROM diplomski_radovi";

        $stmt = $this->_pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


// Dohvaćanje HTML stranice pomoću cURL-a
function dohvatiStranicu($url) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $html = curl_exec($ch);

    if ($html === false) {
        echo "Greška: " . curl_error($ch);
    }

    curl_close($ch);

    return $html;
}


// Dohvaćanje kompletnog teksta pojedinog rada
function dohvatiTekstRada($link) {

    $html = dohvatiStranicu($link);

    if ($html === false || empty($html)) {
        return NULL;
    }

    $dom = new DOMDocument();

    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    $element = $xpath->query(
        "//div[contains(concat(' ', normalize-space(@class), ' '), ' post-content ')]"
    )->item(0);

    if (!$element) {
        return NULL;
    }

    return trim($element->textContent);
}

?>