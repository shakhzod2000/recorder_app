let mediaRecorder; // Holds the MediaRecorder instance
let audioChunks = []; // Stores raw audio data chunks

$(document).ready(function () {
    const startRecord = $('#startRecord');
    const stopRecord = $('.stopRecord');
    const inputField = $('#inputText');
    const status = $('#status');

    startRecord.click(function () {
      // Request microphone access via getUserMedia()
      navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
        mediaRecorder = new MediaRecorder(stream);
        mediaRecorder.start();
        status.text('Aufnahme gestartet...');
        startRecord.prop('disabled', true);
        stopRecord.prop('disabled', false);
        if (inputField.hasClass('d-block')) {
            inputField.removeClass('d-block').addClass('d-none');
        }

        mediaRecorder.ondataavailable = e => {
          audioChunks.push(e.data); // Accumulates raw audio chunks
        };

        mediaRecorder.onstop = () => {
          const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
          const formData = new FormData();

          const receiver = $('#hidden-receiver').val();

          formData.append('audio', audioBlob, 'aufnahme.webm');
          // Dem FormData-Objekt hinzufügen
          formData.append('receiver', receiver);

          status.text('Sende Aufnahme...');

          $.ajax({
            url: 'send.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: () => {
              status.text('✅ Gesendet!');
              // Clear input & reset height
              inputField.val('').css('height', 'auto');
            },
            error: () => status.text('❌ Fehler beim Senden.'),
          });

          audioChunks = [];
        };
      });
    });

    stopRecord.click(function () {
      // Read val from data-receiver and write into hidden-field
      const receiver = $(this).data('receiver');
      $('#hidden-receiver').val(receiver);

      // Audio or Text?

      if (inputField.is(':visible')) {
        // Text mode
        status.text('Sende Notiz...');

        var formData = new FormData();
        var message = inputField.val();
        formData.append('message', message);
        const receiver = $('#hidden-receiver').val();
        // Add receiver to formData
        formData.append('receiver', receiver);
        $.ajax({
          url: 'send.php',
          type: 'POST',
          data: formData, // Contains WebM blob + receiver email
          processData: false,
          contentType: false,
          success: () => {
            status.text('✅ Gesendet!');
            // Clear input & reset height
            inputField.val('').css('height', 'auto');
          },
          error: () => status.text('❌ Fehler beim Senden.'),
        });

      } else {
        // Audio Mode
        mediaRecorder.stop();
      }
      startRecord.prop('disabled', false);
      stopRecord.prop('disabled', true);

    });

    // Toggle Input Field upon clicking on Pen
    $('#toggleInputBtn').click(function() {
        if (inputField.hasClass('d-none')) {
          inputField.removeClass('d-none').addClass('d-block').focus();
        } else {
          inputField.removeClass('d-block').addClass('d-none');
        }
    });

    inputField.on('input', function() {
        // Enable email buttons once user types
        const hasText = $(this).val().trim().length > 0;
        stopRecord.prop('disabled', !hasText);

        // Auto-expand textarea as User types
        this.style.height = 'auto'; // Reset height
        this.style.height = (this.scrollHeight) + 'px'; // Set to new scroll height
    });


});

