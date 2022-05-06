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
                        <div class="window__row">
                            <a href="#" @click.prevent="convertTextToSpeech(lang.from)" class="sound">🔊</a>
                            <div class="window__line">
                                <div class="word">{{ words[counter][lang.from] }}</div>
                            </div>
                        </div>
                        <div class="window__row">
                            <a href="#" @click.prevent="convertTextToSpeech(lang.to)" class="sound" :class="{hide: !isTranslated}">🔊</a>
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

                <div class="main__modal load" v-show="isVisibleModal">
                    <div class="load__box" @drag="dragdrop" ref="draggable">
                        <h3 class="load__message">Загрузите свой Excel документ или перетащите с помощью drag'n
                            drop</h3>
                        <div class="load__area">
                            <div class="load__buttons">
                                <span v-if="userFile" class="green">{{userFile.name}} Готов к загрузке на сервер!</span>
                                <label>
                                    <input type="file" id="table" name="table" ref="file" class="hidden"
                                           @change="hundleUpload">
                                    <div class="btn btn_blue inline">Найти словарь</div>
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
                        <div class="notification" :class="{'bc-green': this.notifications[this.notifications.length - 1].isSuccess, 'bc-red': !this.notifications[this.notifications.length - 1].isSuccess}">
                            <div class="notification__icon">
                                {{this.notifications[this.notifications.length - 1].isSuccess ? '✔' : 'X'}}
                            </div>
                            <div class="notification__content">
                                {{this.notifications[this.notifications.length - 1].content}}
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
                .catch(ex => this.throwNotification('Не удалось загрузить словарь. Попробуйте другой файл'));

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
            let name = [...select.children].find(item => item.selected).innerText;
            return name;
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
        dragdrop(ev) {
            console.log(ev);
        },
    },
    computed: {
        lang() {
            if (this.isRusToEng) {
                return {
                    from: 'rus',
                    to: 'eng',
                }
            } else {
                return {
                    from: 'eng',
                    to: 'rus',
                }
            }
        },
    }
}
</script>

<style scoped>

</style>
