<?php
function skaliereBild(string $pfad, int $groesse_neu): string {
    // Datei- und Verzeichnisinformationen
    $dateiinfos = pathinfo($pfad);
    $dateiname = $dateiinfos["basename"];
    $vzname_neu = $dateiinfos["dirname"] . "/" . $groesse_neu . "x0/";

    // Erstelle das neue Verzeichnis, falls es nicht existiert
    if (!file_exists($vzname_neu)) {
        $ok = mkdir($vzname_neu, 0755, true);
    } else {
        $ok = true;
    }

    // Wenn das Verzeichnis erstellt wurde, fahre mit der Bildskalierung fort
    if ($ok) {
        // Schritt 1: Informationen zum Bild einlesen
        $infos = getimagesize($pfad);
        $w_alt = $infos[0]; // Breite des bestehenden Bildes
        $h_alt = $infos[1]; // Höhe des bestehenden Bildes
        $ar = $w_alt / $h_alt; // Seitenverhältnis des bestehenden (und später auch des neuen) Bildes
        $typ_alt = $infos["mime"]; // MIME-Type des bestehenden Bildes

        // Schritte 2-5: Neue Bildgröße berechnen
        if ($ar > 1) {
            // Querformatbild
            $w_neu = $groesse_neu;
            $h_neu = intval($w_neu / $ar);
        } else {
            // Hochformatbild (oder quadratisch)
            $h_neu = $groesse_neu;
            $w_neu = intval($h_neu * $ar);
        }

        // Bild basierend auf dem MIME-Type skalieren und im neuen Verzeichnis speichern
        switch($typ_alt) {
            case "image/jpeg":
                $r_alt = imagecreatefromjpeg($pfad);
                $r_neu = imagecreatetruecolor($w_neu, $h_neu);
                $r_neu = imagescale($r_alt, $w_neu, $h_neu);
                imagejpeg($r_neu, $vzname_neu . $dateiname);
                break;
            case "image/gif":
                $r_alt = imagecreatefromgif($pfad);
                $r_neu = imagecreate($w_neu, $h_neu);
                $r_neu = imagescale($r_alt, $w_neu, $h_neu);
                imagegif($r_neu, $vzname_neu . $dateiname);
                break;
            case "image/png":
                $r_alt = imagecreatefrompng($pfad);
                $r_neu = imagecreatetruecolor($w_neu, $h_neu);
                $r_neu = imagescale($r_alt, $w_neu, $h_neu);
                imagepng($r_neu, $vzname_neu . $dateiname);
                break;
            case "image/webp":
                $r_alt = imagecreatefromwebp($pfad);
                $r_neu = imagecreatetruecolor($w_neu, $h_neu);
                $r_neu = imagescale($r_alt, $w_neu, $h_neu);
                imagewebp($r_neu, $vzname_neu . $dateiname);
                break;
            case "image/avif":
                $r_alt = imagecreatefromavif($pfad);
                $r_neu = imagecreatetruecolor($w_neu, $h_neu);
                $r_neu = imagescale($r_alt, $w_neu, $h_neu);
                imageavif($r_neu, $vzname_neu . $dateiname);
                break;
        }
    }

    // Rückgabe des Pfads zum neuen Verzeichnis
    return $vzname_neu;
}
?>