import Amplitude from 'amplitudejs';
import Dropzone from 'dropzone/dist/dropzone';

const getRecords = () => {
    let result = [];
    const songId = document.getElementById('song-identifier').getAttribute('data-song-id')
    $.ajax({
        type: "GET",
        async: false,
        url: `/song/${songId}/records`,
        success: function (response) {
            result = JSON.parse(response);
        }
    })
    return result;
}

const addRecord = (record, template) => {
    const index = Amplitude.addSong(record);
    const domElem = $(template)
    domElem.attr('data-amplitude-song-index', index)
    $('#records_list').append(domElem);
}

const initPlayer = () => {
    let recordLinks = document.getElementsByClassName('record-link');

    for (var i = 0; i < recordLinks.length; i++) {
        recordLinks[i].addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }
}
initPlayer()
/*
  Initializes AmplitudeJS
*/
Amplitude.init({
    "songs": getRecords(),
    "volume": 100,
    waveforms: {
        sample_rate: 100
    }
});

const records = getRecords();
console.log(records);


// DROPZONE TO ADD NEW RECORDS
Dropzone.autoDiscover = false;
const dropzoneElem = document.getElementById('file-dropzone');
const fileDrop = new Dropzone('#file-dropzone', {
    url: dropzoneElem.getAttribute('action'),
    acceptedFiles: 'audio/aac, audio/mpeg, .mp3, .aac',
    init: function () {
        this.on("success", function (file) {
            const record = JSON.parse(JSON.parse(file.xhr.responseText).record);
            const template = JSON.parse(file.xhr.responseText).template;
            addRecord(record, template);
            Amplitude.bindNewElements();
            initPlayer()
        });
    }
});




