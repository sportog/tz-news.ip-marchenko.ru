<template>
    <div id="app">
        <vs-row
            vs-align="flex-start"
            vs-type="flex"
            vs-justify="center"
            vs-w="12"
        >
            <vs-col
                vs-type="flex"
                vs-justify="center"
                vs-align="center"
                vs-w="3"
            >
                <vs-card>
                    <div slot="header">
                        <h3>Категории</h3>
                    </div>
                    <div>
                        <vs-alert :active="!categories.length">
                            Список категорий ещё не получен
                        </vs-alert>
                        <categories
                            :categories="categories"
                            category_id_parent="0"
                            :apiCategorySub="apiCategory"
                        ></categories>
                    </div>
                </vs-card>
            </vs-col>
            <vs-col
                vs-justify="center"
                vs-align="center"
                vs-offset="1"
                vs-w="7"
            >
                <vs-card>
                    <div slot="header">
                        <h3>Список новостей</h3>
                    </div>
                    <div>
                        <vs-alert
                            :active="!categorуNews.length && !activeCategoryId"
                        >
                            Выберите категорию для вывода новостей
                        </vs-alert>
                        <vs-alert
                            :active="!categorуNews.length && activeCategoryId"
                        >
                            В категории нет новостей
                        </vs-alert>
                        <vs-button
                            v-for="news in categorуNews"
                            :key="news.id"
                            @click="apiNews(news.id)"
                            class="news"
                            >{{ news.title }}</vs-button
                        >
                    </div>
                </vs-card>
                <div v-show="news">
                    <vs-divider />
                    <vs-card>
                        <div slot="header">
                            <h3>{{ news.title }}</h3>
                        </div>
                        <div>
                            <p class="mb-5">{{ news.content }}</p>
                            <vs-row vs-justify="flex-end">
                                <vs-chip color="#f96854"
                                    >Количество просмотров:
                                    <strong>{{
                                        news.count_reads
                                    }}</strong></vs-chip
                                >
                                <vs-chip color="#24c1a0"
                                    >Сейчас смотрят новость:
                                    <strong>{{
                                        news.online_reads < 1
                                            ? "?"
                                            : news.online_reads
                                    }}</strong></vs-chip
                                >
                            </vs-row>
                        </div>
                    </vs-card>
                </div>
            </vs-col>
        </vs-row>
    </div>
</template>

<script>
document.title = "TZ-News";
import axios from "axios";
import io from "socket.io-client";
import categories from "./components/Categories";
var socket = io("https://tz-news.ip-marchenko.ru:3001", {
    transports: ["websocket", "polling", "flashsocket", "ws", "flash"],
});
export default {
    name: "App",
    data() {
        return {
            activeCategoryId: false,
            categories: [],
            categorуNews: [],
            news: false,
        };
    },
    created() {
        socket.on("COUNT_READS", (data) => {
            if (this.news && this.news.id == data.id)
                this.news.count_reads = data.count_reads;
        });
        socket.on("ONLINE_READS", (data) => {
            for (let newsid in data) {
                if (this.news && this.news.id == newsid)
                    this.news.online_reads = data[newsid];
            }
        });
    },
    mounted() {
        var inside = this;
        axios.get("/api/categories").then(function (response) {
            inside.categories = response.data;
        });
    },
    methods: {
        apiCategory(category_id) {
            var inside = this;
            axios.get("/api/category/" + category_id).then(function (response) {
                inside.categorуNews = response.data;
                inside.news = false;
                inside.activeCategoryId = category_id;
            });
        },
        apiNews(news_id) {
            var inside = this;
            axios.get("/api/news/" + news_id).then(function (response) {
                response.data.online_reads =
                    inside.news && inside.news.id == news_id
                        ? inside.news.online_reads
                        : 0;
                inside.news = response.data;
            });
        },
    },
    watch: {
        "news.id"(val) {
            socket.emit("read news", val);
        },
    },
    components: {
        categories,
    },
};
</script>
<style>
.category,
.news {
    display: inline-table;
    margin-top: 10px;
    width: 100%;
}
</style>
