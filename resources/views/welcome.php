<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= csrf_token() ?>"/>
    <title>Online Photo Adjustments - Adjustments by Adobe Lightroom</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">

</head>
<body>
<section id="app">
    <section class="section">
        <div class="container">
            <section class="hero is-link">
                <div class="hero-body">
                    <p class="title">Online Photo Adjustments</p>
                    <p class="subtitle">Brought to you by Adobe Lightroom</p>
                </div>
            </section>
        </div>
    </section>
    <section id="upload-form" class="section" v-if="!hasUploadedPhoto">
        <form id="upload-photo" @submit.prevent="uploadPhoto()">
            <div class="container">
                <div class="block is-fullwidth" v-if="!showUploadProgressBar">
                    <div class="file is-medium is-boxed is-centered">
                        <label class="file-label">
                            <input class="file-input" type="file" accept="image/jpeg,image/png" name="photo" @change="onFileSelected"/>
                            <span class="file-cta">
                        <span class="file-icon">
                            <i class="fas fa-upload"></i>
                        </span>
                        <span class="file-label"> Select a Photo... </span>
                    </span>
                        </label>
                    </div>
                </div>
                <div class="container is-align-items-center">
                    <div class="block is-half has-text-centered">
                        <div class="control">
                            <button class="button is-primary">
                                <span v-if="!showUploadProgressBar">Upload Photo</span>
                                <span v-else>Uploading...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <section class="section" v-if="hasUploadedPhoto">
        <div class="container">
            <div class="block is-fullwidth">
                <div class="columns is-mobile">
                    <div class="column">
                        <div class="box">
                            <figure class="image is-fullwidth">

                                <img :src="basePhoto"/>
                            </figure>
                        </div>
                    </div>
                    <div class="column" v-if="hasEditedPhoto">
                        <div class="box">
                            <figure class="image is-fullwidth">
                                <img :src="editedPhoto"/>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="upload-progress-bar" class="section" v-if="showUploadProgressBar">
        <div class="container">
            <div class="block is-half">
                <progress class="progress is-normal is-primary" max="100">15%</progress>
            </div>
        </div>
    </section>
    <section class="section" v-if="hasUploadedPhoto">
        <div class="container">
            <div class="block is-half">
                <div class="field is-grouped is-grouped-centered">
                    <p class="control">
                        <button class="button is-primary" @click="autoStraighten()">
                            Straighten Image
                        </button>
                    </p>
                    <p class="control">
                        <button class="button is-primary" @click="autoTone()">
                            Automatic Color Tone Adjustment
                        </button>
                    </p>
                    <p class="control">
                        <button class="button is-primary" @click="toggleAdvancedSettings()">
                            <span>Toggle Advanced Settings...</span>
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section id="advanced-settings" class="section" v-if="showAdvancedSettings">
        <div class="container">
            <div class="block is-fullwidth">
                <div class="columns">
                    <div class="column">
                        Saturation
                    </div>
                    <div class="column">
                        <span class="tag is-medium is-info">{{ saturation }}</span>
                    </div>
                    <div class="column is-three-quarters">
                        <div class="slidecontainer">
                            <input type="range" min="-100" max="100" value="0" class="slider" id="exposure" v-model="saturation">
                        </div>
                    </div>
                    <div class="column"></div>
                </div>
                <div class="columns">
                    <div class="column">
                        Contrast
                    </div>
                    <div class="column">
                        <span class="tag is-medium is-info">{{ contrast }}</span>
                    </div>
                    <div class="column is-three-quarters">
                        <div class="slidecontainer">
                            <input type="range" min="-100" max="100" value="0" class="slider" id="contrast" v-model="contrast">
                        </div>
                    </div>
                    <div class="column"></div>
                </div>
                <div class="columns">
                    <div class="column">
                        Vibrance
                    </div>
                    <div class="column">
                        <span class="tag is-medium is-info">{{ vibrance }}</span>
                    </div>
                    <div class="column is-three-quarters">
                        <div class="slidecontainer">
                            <input type="range" min="-100" max="100" value="0" class="slider" id="vibrance" v-model="vibrance">
                        </div>
                    </div>
                    <div class="column"></div>
                </div>
                <div class="columns">
                    <div class="column">
                        Highlights
                    </div>
                    <div class="column">
                        <span class="tag is-medium is-info">{{ highlights }}</span>
                    </div>
                    <div class="column is-three-quarters">
                        <div class="slidecontainer">
                            <input type="range" min="-100" max="100" value="0" class="slider" id="highlights" v-model="highlights">
                        </div>
                    </div>
                    <div class="column"></div>
                </div>
                <div class="columns">
                    <div class="column">
                        Shadows
                    </div>
                    <div class="column">
                        <span class="tag is-medium is-info">{{ shadows }}</span>
                    </div>
                    <div class="column is-three-quarters">
                        <div class="slidecontainer">
                            <input type="range" min="-100" max="100" value="0" class="slider" id="shadows" v-model="shadows">
                        </div>
                    </div>
                    <div class="column"></div>
                </div>
                <div class="columns">
                    <div class="column">
                        Whites
                    </div>
                    <div class="column">
                        <span class="tag is-medium is-info">{{ whites }}</span>
                    </div>
                    <div class="column is-three-quarters">
                        <div class="slidecontainer">
                            <input type="range" min="-100" max="100" value="0" class="slider" id="whites" v-model="whites">
                        </div>
                    </div>
                    <div class="column"></div>
                </div>
                <div class="columns">
                    <div class="column">
                        Blacks
                    </div>
                    <div class="column">
                        <span class="tag is-medium is-info">{{ blacks }}</span>
                    </div>
                    <div class="column is-three-quarters">
                        <div class="slidecontainer">
                            <input type="range" min="-100" max="100" value="50" class="slider" id="blacks" v-model="blacks">
                        </div>
                    </div>
                    <div class="column"></div>
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    Clarity
                </div>
                <div class="column">
                    <span class="tag is-medium is-info">{{ clarity }}</span>
                </div>
                <div class="column is-three-quarters">
                    <div class="slidecontainer">
                        <input type="range" min="-100" max="100" value="50" class="slider" id="blacks" v-model="clarity">
                    </div>
                </div>
                <div class="column"></div>
            </div>
        </div>
        <div class="block is-half has-text-centered">
            <div class="control">
                <button class="button is-primary" @click="applyEdits()">
                    <span>Apply Changes</span>
                </button>
            </div>
        </div>

        </div>
    </section>
    <section class="section" v-if="hasUploadedPhoto">
        <div class="container">
            <div class="block is-half">
                <div class="field">
                    <p class="control has-text-centered">
                        <button class="button is-light" @click="resetForm()">
                            Start Over
                        </button>
                    </p>
                </div>

            </div>
        </div>
    </section>
    <footer class="footer">
        <div class="content has-text-centered">
            <p>
                Site by <strong>Ryan Street</strong>.
                &copy; Copyright 2024.
            </p>
        </div>
    </footer>
</section>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
