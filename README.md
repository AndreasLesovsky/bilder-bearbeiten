Bilder zuschneiden und Skalieren mittels PHP

Im Bilder-Bearbeiten-Projekt wurde eine PHP-Anwendung erstellt, die es ermöglicht, Bilder direkt im Browser zu bearbeiten. Die "skaliereBild"-Funktion ermöglicht es, das Bild in mehreren Zielgrößen zu speichern. Die bearbeiteten Versionen werden in entsprechenden Ordnern auf dem Server gespeichert und können direkt heruntergeladen werden.

Die Anwendung nutzt ausserdem die imagecrop-Funktion von PHP, um von einem hochgeladenen Bild Pixel von allen vier Seiten zu entfernen. Eine Bildvorschau zeigt die gesamte Höhe und Breite des Originals an, sodass der Benutzer sehen kann, wie viel vom Bild entfernt werden kann. Bei Eingabe eines zu großen Wertes erfolgt eine entsprechende Fehlermeldung, um sicherzustellen, dass die Zuschneideoperation innerhalb der zulässigen Grenzen bleibt.

Zusätzlich kann zwischen Zuschneiden und Skalieren umgeschaltet werden. Die skaliereBild-Funktion ermöglicht es, das Bild in mehreren Zielgrößen zu speichern. Die bearbeiteten Versionen werden in entsprechenden Ordnern auf dem Server gespeichert und können direkt heruntergeladen werden.

Installation

Über xampp oder einen anderen Webserver der PHP unterstützt hosten. Die GD Erweiterung muss in der php.ini aktiviert werden.

Schritte, um das Projekt lokal zu installieren:

Repository klonen: git clone https://github.com/andreaslesovsky/bilder-beschneiden.git

Autoren

Andreas Lesovsky

Lizenz

Dieses Projekt steht unter der MIT-Lizenz.
