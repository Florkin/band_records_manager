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


const initPlayer = () => {
    let recordLinks = document.getElementsByClassName('record-link');

    for (var i = 0; i < recordLinks.length; i++) {
        recordLinks[i].addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }


    let songElements = document.getElementsByClassName('song');

    for (var i = 0; i < songElements.length; i++) {
        /*
          Ensure that on mouseover, CSS styles don't get messed up for active songs.
        */
        songElements[i].addEventListener('mouseover', function () {
            this.style.backgroundColor = '#00A0FF';

            this.querySelectorAll('.song-meta-data .song-title')[0].style.color = '#FFFFFF';

            if (!this.classList.contains('amplitude-active-song-container')) {
                this.querySelectorAll('.play-button-container')[0].style.display = 'block';
            }

            // this.querySelectorAll('img.bandcamp-grey')[0].style.display = 'none';
            // this.querySelectorAll('img.bandcamp-white')[0].style.display = 'block';
            // this.querySelectorAll('.song-duration')[0].style.color = '#FFFFFF';
        });

        /*
          Ensure that on mouseout, CSS styles don't get messed up for active songs.
        */
        songElements[i].addEventListener('mouseout', function () {
            this.style.backgroundColor = '#FFFFFF';
            this.querySelectorAll('.song-meta-data .song-title')[0].style.color = '#272726';
            this.querySelectorAll('.play-button-container')[0].style.display = 'none';
        });

        /*
          Show and hide the play button container on the song when the song is clicked.
        */
        songElements[i].addEventListener('click', function () {
            this.querySelectorAll('.play-button-container')[0].style.display = 'none';
        });
    }
}

initPlayer()
/*
  Initializes AmplitudeJS
*/
Amplitude.init({
    "songs": getRecords(),
    "callbacks": {
        'play': function () {
            console.log("play")
        },

        'pause': function () {
            console.log("pause")
        }
    },
    waveforms: {
        sample_rate: 100
    }
});


Dropzone.autoDiscover = false;

const dropzoneElem = document.getElementById('file-dropzone');
const fileDrop = new Dropzone('#file-dropzone', {
    url: dropzoneElem.getAttribute('action'),
    acceptedFiles: 'audio/aac, audio/mpeg, .mp3, .aac',
    init: function() {
        this.on("success", function(file) {
            const record = JSON.parse(JSON.parse(file.xhr.responseText).record);
            const template = JSON.parse(file.xhr.responseText).template;
            const index = Amplitude.addSong(record);

            const domElem = $(template)
            domElem.attr('data-amplitude-song-index', index)
            $('#amplitude-list').append(domElem);
            Amplitude.bindNewElements();
            initPlayer()
        });
    }
});




