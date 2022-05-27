<template>
    <main class="main">
        <div class="main__container container">
            <div class="main__body">
                <div class="main__top">
                    <div class="main__lang">{{ lang.from.toUpperCase() }} -> {{ lang.to.toUpperCase() }}</div>
                    <div class="main__interface">
                        <div class="main__buttons">
                            <div class="choice-word-list">
                                <div class="select-helper" :class="{'hide': currentDictionaryId !== null}">Выбрать
                                    словарь:
                                </div>
                                <select v-model="currentDictionaryId" @change="downloadTable">
                                    <option v-for="item in wordListsNames" :value="item.id">{{ item.name }}</option>
                                </select>
                            </div>
                            <button class="choice btn btn_red" @click="showModalWindow">Загрузить словарь</button>
                        </div>
                        <div class="main__switcher switcher">
                            <input type="checkbox" id="checkbox" v-model="isRusToEng">
                            <label for="checkbox" class="switcher__label">
                                <span class="switcher__left"></span>
                                <span class="switcher__right"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="main__window window">
                    <div class="window__content" v-if="words">
                        <div class="window__image" v-if="words[counter]['image']">
                            <img :src="words[counter]['image']" alt="image">
                        </div>
                        <div class="window__row">
                            <a href="#" @click.prevent="convertTextToSpeech(lang.from)" class="sound">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     height="30"
                                     width="30"
                                     viewBox="0 0 456.373 456.373">
	                                <path stroke-width="3" stroke="#000" d="M397.83,1.156c-5.665-2.349-12.192-1.05-16.532,3.288L277.3,108.442H168.923c-65.884,0-119.745,53.598-119.745,119.745    c0,66.028,53.717,119.745,119.745,119.745H277.3L381.298,451.93c4.345,4.346,10.874,5.633,16.532,3.288    c5.669-2.348,9.365-7.879,9.365-14.015V15.171C407.195,9.035,403.5,3.503,397.83,1.156z M138.883,312.393    c-34.565-12.367-59.365-45.443-59.365-84.206c0-38.764,24.8-71.839,59.365-84.207V312.393z M268.413,317.59h-99.189V138.782    h99.189V317.59z M376.855,404.578l-78.101-78.101V129.896l78.101-78.101V404.578z"/>
                                </svg>
                            </a>
                            <div class="window__line">
                                <div class="word">{{ words[counter][lang.from] }}</div>
                            </div>
                        </div>
                        <div class="window__row">
                            <a href="#" @click.prevent="convertTextToSpeech(lang.to)" class="sound"
                               :class="{hide: !isTranslated}">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     height="30"
                                     width="30"
                                     viewBox="0 0 456.373 456.373">
                                    <path stroke-width="3" stroke="#000" d="M397.83,1.156c-5.665-2.349-12.192-1.05-16.532,3.288L277.3,108.442H168.923c-65.884,0-119.745,53.598-119.745,119.745    c0,66.028,53.717,119.745,119.745,119.745H277.3L381.298,451.93c4.345,4.346,10.874,5.633,16.532,3.288    c5.669-2.348,9.365-7.879,9.365-14.015V15.171C407.195,9.035,403.5,3.503,397.83,1.156z M138.883,312.393    c-34.565-12.367-59.365-45.443-59.365-84.206c0-38.764,24.8-71.839,59.365-84.207V312.393z M268.413,317.59h-99.189V138.782    h99.189V317.59z M376.855,404.578l-78.101-78.101V129.896l78.101-78.101V404.578z"/>
                                </svg>
                            </a>
                            <div class="window__line">
                                <div class="word questions">{{ isTranslated ? words[counter][lang.to] : '???' }}</div>
                            </div>
                        </div>
                        <div class="window__buttons">
                            <button class="button button_check" @click="isTranslated = true">Показать перевод</button>
                            <button class="button button_next" @click="incrementCounter">Следующее слово →</button>
                        </div>
                        <div class="window__info">
                            <div class="bold">Текущий словарь:</div>
                            <div class="light">{{ getDictionaryName() }}</div>
                        </div>
                    </div>
                    <div v-else class="_red">Слов нет. Пожалуйста, выберите словарь который хотите использовать.</div>
                </div>

                <div class="main__modal load" v-show="isVisibleModal" @click="disableModalVisible">
                    <div class="load__box" @drag="dragdrop" ref="draggable" @click.stop>
                        <h3 class="load__message">Загрузите свой Excel документ или перетащите с помощью drag'n
                            drop <br> Так же по желанию можете выгрузить zip архив с фотографиями (имена фотографий
                            должны совпадать с английской версией слова)</h3>
                        <div class="load__area">
                            <div class="load__buttons">
                                <span v-if="userFile"
                                      class="green">{{ userFile.name }} Готов к загрузке на сервер!</span>
                                <label>
                                    <input type="file" id="table" name="table" ref="file" class="hidden"
                                           @change="hundleUpload">
                                    <div class="btn btn_blue inline">Найти словарь</div>
                                </label>

                                <label>
                                    <input type="file" id="image" name="image" ref="image" class="hidden"
                                           @change="hundleUploadImage">
                                    <div class="btn btn_blue inline">Найти zip архив (необязательно)</div>
                                </label>

                                <button class="btn btn_red" @click="loadTable">Загрузить словарь
                                </button>
                            </div>
                        </div>
                        <a href="#" class="load__cancel" @click.prevent="disableModalVisible">X</a>
                    </div>
                </div>
                <Transition>
                    <template v-if="this.notifications.length">
                        <div class="notification"
                             :class="{'bc-green': this.notifications[this.notifications.length - 1].isSuccess, 'bc-red': !this.notifications[this.notifications.length - 1].isSuccess}">
                            <div class="notification__icon">
                                {{ this.notifications[this.notifications.length - 1].isSuccess ? '✔' : 'X' }}
                            </div>
                            <div class="notification__content">
                                {{ this.notifications[this.notifications.length - 1].content }}
                            </div>
                        </div>
                    </template>
                </Transition>
            </div>
        </div>
    </main>
</template>

<script>
export default {
    name: "Main",
    components: {},

    data() {
        return {
            isVisibleModal: false,
            userFile: null,
            wordListsNames: [],
            currentDictionaryId: null,
            currentDictionary: null,
            words: null,
            counter: 0,
            isRusToEng: false,
            isTranslated: false,
            notifications: [],
            userZip: null,
        }
    },
    mounted() {
        this.getWordListNames();
        this.dragConfig();
    },
    methods: {
        dragConfig() {
            ['drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop'].forEach(item => {
                this.$refs.draggable.addEventListener(item, (ev) => {
                    if (item === 'dragstart' || item === 'dragenter' || item === 'dragover') {
                        this.$refs.draggable.classList.add('animate');
                    } else {
                        this.$refs.draggable.classList.remove('animate');
                    }
                    ev.stopPropagation();
                    ev.preventDefault();
                    return false;
                });
            });
            this.$refs['draggable'].addEventListener('drop', (e) => {
                this.userFile = e.dataTransfer.files[0];
            });
        },
        showModalWindow() {
            this.isVisibleModal = true;
        },
        disableModalVisible() {
            this.isVisibleModal = false;
        },
        hundleUpload() {
            this.userFile = this.$refs.file.files[0];
        },
        hundleUploadImage() {
            this.userZip = this.$refs.image.files[0];
        },
        throwNotification(content, mode) {
            const notification = {
                content: content,
                isSuccess: mode === 'Success',
            };

            this.notifications.push(notification);

            setTimeout(() => this.notifications.shift(), 3000);
        },
        loadTable() {
            const formData = new FormData();
            formData.append('table', this.userFile);
            if (this.userZip) {
                formData.append('images', this.userZip);
            }

            axios.post('api/main/file', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
            })
                .then(res => {
                    if (res) {
                        this.throwNotification('Словарь был успешно добавлен!', 'Success');
                        this.wordListsNames.push(res.data);
                    }
                })
                .catch(ex => {
                    this.throwNotification('Не удалось загрузить словарь. Попробуйте другой файл');
                });

            this.isVisibleModal = false;
        },
        async getWordListNames() {
            axios.get('api/main/words')
                .then(res => {
                    this.wordListsNames = res.data;
                })
                .catch(err => console.log(err));
        },
        downloadTable() {
            axios.get('api/main/dictionary/' + this.currentDictionaryId)
                .then(res => {
                    this.words = res.data;
                    this.getDictionaryName();
                })
                .catch(err => console.log(err));
        },
        incrementCounter() {
            this.isTranslated = false;

            if (this.counter >= this.words.length - 1) {
                this.counter = 0;
            } else {
                this.counter++;
            }
        },
        getDictionaryName() {
            const select = document.querySelector('.choice-word-list select');

            return [...select.children].find(item => item.selected).innerText;
        },
        convertTextToSpeech(mode) {
            window.speechSynthesis.getVoices();

            setTimeout(() => {
                let voices = window.speechSynthesis.getVoices();
                console.log(voices);
                const speaker = new SpeechSynthesisUtterance();
                const voice = voices.find(voice => voice.name === `Google ${mode === 'rus' ? 'русский' : 'US English'}`);
                speaker.text = this.words[this.counter][`${mode}`];
                speaker.voice = voice;
                speaker.lang = voice.lang;
                speaker.volume = 100;
                speechSynthesis.cancel();
                speechSynthesis.speak(speaker);
            }, 50);
        },
    },
    computed: {
        lang() {
            return this.isRusToEng ? {from: 'rus', to: 'eng'} : {from: 'eng', to: 'rus'};
        },
    },
}
</script>

<style scoped>

</style>
