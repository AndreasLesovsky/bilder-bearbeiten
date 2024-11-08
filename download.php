<?php
if (isset($_GET['session_id']) && isset($_GET['date']) && isset($_GET['original_name'])) {
    $sessionId = basename($_GET['session_id']);
    $date = basename($_GET['date']);
    $originalName = basename($_GET['original_name']);
    $dir = "./bilder/$date/$sessionId/{$originalName}_all_sizes";

    if (is_dir($dir)) {
        // Name des ZIP-Archivs
        $zipFileName = "{$originalName}_all_sizes.zip";

        // Erstelle ein neues ZipArchive-Objekt
        $zip = new ZipArchive();
        
        // Öffne das Zip-Archiv zur Erstellung im temporären Verzeichnis
        $tempZipFile = tempnam(sys_get_temp_dir(), 'zip');
        if ($zip->open($tempZipFile, ZipArchive::CREATE) !== TRUE) {
            exit("Kann das ZIP-Archiv nicht erstellen.");
        }

        // Iteriere durch alle Dateien im Verzeichnis und füge sie zum ZIP-Archiv hinzu
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($dir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Schließe das Zip-Archiv
        $zip->close();

        // Setze die Header für den Download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
        header('Content-Length: ' . filesize($tempZipFile));

        // Lese die temporäre ZIP-Datei und sende sie an den Browser
        readfile($tempZipFile);

        // Lösche die temporäre Datei
        unlink($tempZipFile);

        // Beende das Skript
        exit;
    } else {
        echo "Der angeforderte Ordner existiert nicht.";
    }
} else {
    echo "Unzureichende Parameter.";
}
?>