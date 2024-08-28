const app = Vue.createApp({
    data() {
        return {
            csrfToken: null,
            showAdvancedSettings: false,
            showUploadProgressBar: false,
            hasUploadedPhoto: false,
            hasEditedPhoto: false,
            localPhoto: null,
            originalImageName: null,
            editedImageName: null,
            lastJobId: null,
            basePhoto: null,
            editedPhoto: null,
            saturation: 0,
            contrast: 0,
            vibrance: 0,
            highlights: 0,
            shadows: 0,
            whites: 0,
            blacks: 0,
            clarity: 0
        }
    },
    methods: {
        /**
         * Uploads photo to S3
         */
        uploadPhoto() {
            this.toggleUploadProgressBar();
            let formData = new FormData();

            formData.append('photo', this.localPhoto);

            fetch('/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: formData
            })
                .then(res => res.json())
                .then((body) => {
                    this.basePhoto = body.photo;
                    this.originalImageName = body.original_image_name
                    this.toggleUploadProgressBar();
                    this.toggleHasUploadedPhoto();
                })
        },
        /**
         * Calls Auto-straighten API
         */
        autoStraighten() {
            this.toggleUploadProgressBar();
            let formData = new FormData();
            formData.append('original_image_name', this.originalImageName);

            fetch('/autoStraighten', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: formData
            })
                .then(res => res.json())
                .then((body) => {
                        console.log(body);
                        this.lastJobId = body.job_id;
                        this.editedImageName = body.edited_image_name;
                        this.checkJobStatus(this.lastJobId);
                    }
                )
        },
        /**
         * Calls Auto Tone API
         */
        autoTone() {
            this.toggleUploadProgressBar();
            let formData = new FormData();
            formData.append('original_image_name', this.originalImageName);

            fetch('/autoTone', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: formData
            })
                .then(res => res.json())
                .then((body) => {
                        console.log(body);
                        this.lastJobId = body.job_id;
                        this.editedImageName = body.edited_image_name;
                        this.checkJobStatus(this.lastJobId);
                    }
                )
        },
        /**
         * Calls Advanced Edit API
         */
        applyEdits() {
            this.toggleUploadProgressBar();
            let formData = new FormData();
            formData.append('original_image_name', this.originalImageName);
            formData.append('saturation', this.saturation);
            formData.append('contrast', this.contrast);
            formData.append('vibrance', this.vibrance);
            formData.append('highlights', this.highlights);
            formData.append('shadows', this.shadows);
            formData.append('whites', this.whites);
            formData.append('blacks', this.blacks);
            formData.append('clarity', this.clarity);

            fetch('/applyEdits', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: formData
            })
                .then(res => res.json())
                .then((body) => {
                        console.log(body);
                        this.lastJobId = body.job_id;
                        this.editedImageName = body.edited_image_name;
                        this.checkJobStatus(this.lastJobId);
                    }
                )
        },
        /**
         * Checks current job status.  Recursive.
         *
         * @param $jobId
         */
        checkJobStatus($jobId) {
            fetch('/getJobStatus/' + this.lastJobId, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                }
            })
                .then(res => res.json())
                .then((body) => {
                        console.log(body.status);
                        switch (body.status) {
                            case 'succeeded':
                                this.hasEditedPhoto = true;
                                this.getEditedImageUrl(this.editedImageName);
                                this.toggleUploadProgressBar();
                                break;
                            case 'failed':
                                // throw error
                                break;
                            default:
                                setTimeout(this.checkJobStatus, 2000, this.lastJobId);
                        }
                    }
                )

        },
        /**
         * Returns the final edited image from S3
         *
         * @param imageName
         */
        getEditedImageUrl(imageName) {
            fetch('/getEditedImageName/' + encodeURI(imageName), {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                }
            })
                .then(res => res.json())
                .then((body) => {
                    this.editedPhoto = body.image_url;
                });
        },
        /**
         * Sets photo into variable in preparing for upload.
         *
         * @param event
         */
        onFileSelected(event) {
            this.localPhoto = event.target.files[0];
        },
        /**
         * Toggles the Advanced Settings on page for Advanced Edits
         */
        toggleAdvancedSettings() {
            this.showAdvancedSettings = !this.showAdvancedSettings
        }
        ,
        /**
         * Toggles the upload progress bar when requests or jobs are called.
         */
        toggleUploadProgressBar() {
            this.showUploadProgressBar = !this.showUploadProgressBar
        }
        ,
        /**
         * Signifies when the photo has been uploaded.
         */
        toggleHasUploadedPhoto() {
            this.hasUploadedPhoto = !this.hasUploadedPhoto
        },
        /**
         * Toggles when a photo has come back from the API
         */
        toggleHasEditedPhoto() {
            this.hasEditedPhoto = !this.hasEditedPhoto;
        },
        /**
         * Resets the form (Start Over button)
         */
        resetForm() {
            this.showAdvancedSettings = false;
            this.showUploadProgressBar = false;
            this.hasUploadedPhoto = false;
            this.hasEditedPhoto = false;
            this.localPhoto = null;
            this.originalImageName = null;
            this.editedImageName = null;
            this.lastJobId = null;
            this.basePhoto = null;
            this.editedPhoto = null;
            this.saturation = 0;
            this.contrast = 0;
            this.vibrance = 0;
            this.highlights = 0;
            this.shadows = 0;
            this.whites = 0;
            this.blacks = 0;
            this.clarity = 0;
        }
    },
    mounted() {
        // pulls the CSRF token for authentication when submitting fetch() requests
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')['content'];
    }
})

app.mount('#app');
