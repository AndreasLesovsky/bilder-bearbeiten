<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/bilder.inc.php"); // enthält Funktion skaliereBild

$whitelist = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
$msg = "";

// Überprüfen, ob Formulardaten vorhanden sind
if (count($_POST) > 0) {
    // Bild Zuschneiden
    if (isset($_FILES['bildCrop'])) {

        $uploadedImage = $_FILES['bildCrop'];
        $cropLeft = intval($_POST['links']);
        $cropTop = intval($_POST['oben']);
        $cropRight = intval($_POST['rechts']);
        $cropBottom = intval($_POST['unten']);

        if (in_array($uploadedImage['type'], $whitelist)) {
            $tmpName = $uploadedImage['tmp_name'];
            $originalName = basename($uploadedImage['name']);
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);

            $date = date('Ymd');
            session_start();
            $sessionId = session_id();
            $dir = "./bilder/$date/$sessionId";
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $originalPath = "$dir/$originalName";
            move_uploaded_file($tmpName, $originalPath);

            $image = null;
            switch ($uploadedImage['type']) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($originalPath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($originalPath);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($originalPath);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($originalPath);
                    break;
                case 'image/bmp':
                    $image = imagecreatefrombmp($originalPath);
                    break;
            }

            $info = getimagesize($originalPath);
            $w_old = $info[0];
            $h_old = $info[1];

            if ($image) {
                $cropWidth = $w_old - $cropRight - $cropLeft;
                $cropHeight = $h_old - $cropBottom - $cropTop;

                $croppedImage = imagecrop($image, ['x' => $cropLeft, 'y' => $cropTop, 'width' => $cropWidth, 'height' => $cropHeight]);

                if ($croppedImage !== FALSE) {
                    $croppedPath = "$dir/" . pathinfo($originalName, PATHINFO_FILENAME) . "_cropped.png";
                    imagepng($croppedImage, $croppedPath);
                    imagedestroy($croppedImage);

                    $msg .= "<p class='col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12'><strong>Zugeschnittenes Bild:</strong></p>
                            <img src='$croppedPath' alt='Zugeschnittenes Bild' class='col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12' id='cropped-image'>
                            <p class='success col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12'>Das Bild wurde erfolgreich zugeschnitten und gespeichert! Sie können es herunterladen.</p>
                            <a href='$croppedPath' download class='link-btn'>Bild herunterladen</a>";
                } else {
                    $msg .= "<p class='error col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12'>Bild konnte nicht zugeschnitten werden. Bitte geben Sie gültige Werte ein, die innerhalb der zulässigen Grenzen sind.</p>";
                }

                imagedestroy($image);
            }
        } else {
            $msg = "<p class='error col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12'>Ungültiges Dateiformat. Erlaubt sind nur JPEG, PNG, WebP, GIF und BMP Dateien.</p>";
        }
    }

    // Bild Skalieren
    if (isset($_FILES['bildScale'])) {
        $uploadedImage = $_FILES['bildScale'];
    
        $date = date('Ymd');
        session_start();
        $sessionId = session_id();
        $originalName = basename($uploadedImage['name']);
        $dir = "./bilder/$date/$sessionId/{$originalName}_all_sizes";
    
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    
        $tmpName = $uploadedImage['tmp_name'];
        $originalPath = "$dir/$originalName";
        move_uploaded_file($tmpName, $originalPath);
    
        $sizes = [
            intval($_POST['groesse_1']),
            intval($_POST['groesse_2']),
            intval($_POST['groesse_3']),
            intval($_POST['groesse_4']),
        ];
    
        foreach ($sizes as $size) {
            if ($size > 0) {
                // Erstelle Pfad für jedes skalierte Bild und speichere den Rückgabewert
                skaliereBild($originalPath, $size);
            }
        }
    
        // Verlinke auf download.php, die das ZIP direkt im Speicher erstellt und beim Download ausgibt
        $msg .= "<p class='success col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                    Skalierte Bilder wurden in Ordnern gespeichert und stehen zum Download bereit!
                </p>
                <p class='col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                    <a href='download.php?session_id=" . urlencode($sessionId) . "&date=" . urlencode($date) . "&original_name=" . urlencode($originalName) . "' class='link-btn'>Bilder herunterladen</a>
                </p>";
    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=yes">
    <meta name="description" content="Bilder bearbeiten mit PHP">
    <title>Bilder bearbeiten mit PHP</title>
    <link rel="stylesheet" href="css/importer.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/config.css">
    <link rel="stylesheet" href="css/grid.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/media.css">
    <link rel="icon" href="icon.svg">
    <script src="script.js"></script>
</head>

<body>
    <header>
        <div class="grid">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h1>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="#ff3f2e"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm40-80h480L570-480 450-320l-90-120-120 160Zm-40 80v-560 560Z"/></svg>
                    Bilder bearbeiten
                </h1>
                <select id="contentSwitcher">
                    <option value="content1" selected>Zuschneiden</option>
                    <option value="content2">Skalieren</option>
                </select>
            </div>
        </div>
    </header>
    <main>

        <section id="content1" class="grid">
            <h2 class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">Zuschneiden</h2>
            <?php echo ($msg); ?>
            <form method="post" enctype="multipart/form-data" class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 grid">

                <div class="image-upload-container col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="custom-file-upload">
                        <label title="Klicken zum Auswählen eines Bildes">Bild zum Zuschneiden:</label>
                        <button type="button" class="upload-button" title="Klicken zum Auswählen eines Bildes">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="var(--col-font)">
                                <path d="M440-320v-326L336-542l-56-58 200-200 200 200-56 58-104-104v326h-80ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z" />
                            </svg>
                        </button>
                        <button type="button" class="clear-button" title="Auswahl löschen">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="var(--col-font)">
                                <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                            </svg>
                        </button>
                        <input type="file" name="bildCrop" class="file-input" accept="image/jpeg, image/png, image/webp, image/gif, image/bmp" required>
                    </div>

                    <div class="preview-container">
                        <img class="bild-preview" src="#" alt="Bildvorschau">
                        <div class="bild-name">Keine Datei ausgewählt</div>
                    </div>
                </div>

                <fieldset class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 grid">
                    <legend class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        Werte für das Zuschneiden in Pixeln eingeben:
                    </legend>
                    <label class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        Von links wegschneiden
                        <input type="number" name="links" placeholder="px">
                    </label>
                    <label class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        Von oben wegschneiden
                        <input type="number" name="oben" placeholder="px">
                    </label>
                    <label class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        Von rechts wegschneiden
                        <input type="number" name="rechts" placeholder="px">
                    </label>
                    <label class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        Von unten wegschneiden
                        <input type="number" name="unten" placeholder="px">
                    </label>
                </fieldset>
                <input type="submit" value="zuschneiden" class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
            </form>
        </section>

        <section id="content2" class="grid" style="display:none;">
            <h2 class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">Skalieren</h2>
            <?php echo ($msg); ?>
            <form method="POST" enctype="multipart/form-data" class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 grid">
                <div class="image-upload-container col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="custom-file-upload">
                        <label title="Klicken zum Auswählen eines Bildes">Bild zum Skalieren:</label>
                        <button type="button" class="upload-button" title="Klicken zum Auswählen eines Bildes">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="var(--col-font)">
                                <path d="M440-320v-326L336-542l-56-58 200-200 200 200-56 58-104-104v326h-80ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z" />
                            </svg>
                        </button>
                        <button type="button" class="clear-button" title="Auswahl löschen">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="var(--col-font)">
                                <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                            </svg>
                        </button>
                        <input type="file" name="bildScale" class="file-input" accept="image/jpeg, image/png, image/webp, image/gif, image/bmp" required>
                    </div>

                    <div class="preview-container">
                        <img class="bild-preview" src="#" alt="Bildvorschau">
                        <div class="bild-name">Keine Datei ausgewählt</div>
                    </div>
                </div>

                <fieldset class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 grid">
                    <legend class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            Neue Größe in Pixeln eingeben:
                    </legend>
                    <label class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        Größe 1:
                        <input type="number" id="groesse_1" name="groesse_1" min="1" placeholder="Breite in Pixel">
                    </label>
                    <label class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        Größe 2:
                        <input type="number" id="groesse_2" name="groesse_2" min="1" placeholder="Breite in Pixel">
                    </label>
                    <label class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        Größe 3:
                        <input type="number" id="groesse_3" name="groesse_3" min="1" placeholder="Breite in Pixel">
                    </label>
                    <label class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        Größe 4:
                        <input type="number" id="groesse_4" name="groesse_4" min="1" placeholder="Breite in Pixel">
                    </label>
                </fieldset>
                <input type="submit" value="skalieren" class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
            </form>

        </section>
    </main>
</body>

</html>