document.addEventListener("DOMContentLoaded", () => {
  // Main Switcher
  const switcher = document.getElementById('contentSwitcher');
    const content1 = document.getElementById('content1');
    const content2 = document.getElementById('content2');

    // Event Listener für den Wechsel des Auswahlfelds
    switcher.addEventListener('change', function() {
      if (this.value === 'content1') {
        content1.style.display = 'grid';
        content2.style.display = 'none';
      } else if (this.value === 'content2') {
        content1.style.display = 'none';
        content2.style.display = 'grid';
      }
    });

    // Initialer Zustand basierend auf der vorausgewählten Option
    if (switcher.value === 'content1') {
      content1.style.display = 'grid';
      content2.style.display = 'none';
    } else {
      content1.style.display = 'none';
      content2.style.display = 'grid';
    }




  // Handler für alle Upload-Buttons
    document.querySelectorAll(".upload-button").forEach(button => {
        button.addEventListener("click", function() {
            const container = this.closest(".image-upload-container");
            const fileInput = container.querySelector(".file-input");
            fileInput.click();
        });
    });

    // Handler für alle Clear-Buttons
    document.querySelectorAll(".clear-button").forEach(button => {
        button.addEventListener("click", function() {
            const container = this.closest(".image-upload-container");
            const fileInput = container.querySelector(".file-input");
            fileInput.value = "";
            const preview = container.querySelector(".bild-preview");
            const fileName = container.querySelector(".bild-name");
            fileName.textContent = "Keine Datei ausgewählt";
            preview.style.display = "none";
        });
    });

    // Handler für alle File Inputs
    document.querySelectorAll(".file-input").forEach(input => {
        input.addEventListener("change", function(event) {
            const file = event.target.files[0];
            const container = this.closest(".image-upload-container");
            const preview = container.querySelector(".bild-preview");
            const fileName = container.querySelector(".bild-name");

            if (file) {
                const fileNameText = file.name;
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";

                    // Erstellt ein neues Bildobjekt, um die Größe des Bildes zu bestimmen
                    const img = new Image();
                    img.onload = function() {
                        const width = img.width;
                        const height = img.height;
                        fileName.textContent = `${fileNameText} (${width}x${height}px)`;
                    };
                    img.src = e.target.result;
                };

                reader.readAsDataURL(file);
            } else {
                fileName.textContent = "Keine Datei ausgewählt";
                preview.style.display = "none";
            }
        });
    });
});
