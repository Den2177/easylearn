<template>
    <div class="container">
        <div class="word-page">
            <div class="prev">
                <router-link class="link" :to="{name: 'main.index'}">Назад</router-link></div>
            <div class="search">
                <input type="text" class="input" placeholder="Поиск..." v-model="userSearch">
                <svg class="icon" viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" focusable="false" role="none"
                     style="pointer-events: none; display: block; width: 30px; height: 30px;">
                    <g>
                        <path
                            d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z">
                        </path>
                    </g>
                </svg>
            </div>
            <div class="tables" v-if="dictionaries">
                <table v-for="table in search">
                    <h3>{{ table.name }}</h3>
                    <tr>
                        <th>ENG</th>
                        <th>RUS</th>
                    </tr>
                    <tr v-for="word in table.words">
                        <td v-html="getDecoratedText(word.eng)"></td>
                        <td v-html="getDecoratedText(word.rus)"></td>
                    </tr>
                    <tr class="button-wrap" @click="deleteDict(table.id)">
                        <td>
                            <button class="btn btn_red">Удалить словарь</button>
                        </td>
                    </tr>
                </table>
            </div>
            <Transition>
                <template v-if="notification">
                    <div class="notification" :class="{'bc-green': notification.isSuccess, 'bc-red': !notification.isSuccess}">
                        <div class="notification__icon">
                            {{notification.isSuccess ? '✔' : 'X'}}
                        </div>
                        <div class="notification__content">
                            {{notification.content}}
                        </div>
                    </div>
                </template>
            </Transition>
        </div>
    </div>

</template>

<script>
export default {
    name: "Words",
    data() {
        return {
            dictionaries: null,
            userSearch: '',
            notification: null,
        }
    },
    mounted() {
        this.getAllDictionaries();
    },
    methods: {
        getAllDictionaries() {
            axios.get('/api/words/dictionaries')
                .then(res => {
                    this.dictionaries = res.data;
                })
                .catch(err => console.log(err));
        },
        getDecoratedText(text) {
            let pos = text.search(this.userSearch);
            if (pos === -1) {
                return text;
            }

            text = text.slice(0, pos) + '<mark>' + text.slice(pos, pos + this.userSearch.length) + '</mark>' + text.slice(pos + this.userSearch.length);
            return text;
        },
        deleteDict(id) {
            axios.delete('/api/words/' + id + '/delete').then(res => {
                this.throwNotification('Словарь был успешно удален', 'Success');
                this.dictionaries = this.dictionaries.filter(item => item.id !== id);
            }).catch(err => {
                this.throwNotification('Не удалось удалить словарь');
            })
        },
        throwNotification(content, mode) {
            this.notification = {
                content: content,
                isSuccess: mode === 'Success',
            };

            setTimeout(() => this.notification = null, 5000);
        },

    },
    computed: {
        search() {
            return this.dictionaries
                .filter(dict => dict.words.some(word => word['eng'].toUpperCase().includes(this.userSearch.toUpperCase())
                    || word['rus'].toUpperCase().includes(this.userSearch.toUpperCase())));
        }
    }
}
</script>

<style scoped>

</style>
