var app = require('express')();
var fs = require('fs');
var options = {
	key: fs.readFileSync('/var/www/httpd-cert/fioru/tz-news.ip-marchenko.ru_le1.key'),
	cert: fs.readFileSync('/var/www/httpd-cert/fioru/tz-news.ip-marchenko.ru_le1.crt')
};
var server = require('https').createServer(options, app);
var io = require('socket.io')(server);

var sockets = {};
var onlineNews = {};

function refreshOnlineNews() {
	onlineNews = {};
	Object.values(sockets).forEach(news_id => {
		if (news_id > 0) {
			if (typeof onlineNews[news_id] === 'undefined')
				onlineNews[news_id] = 0;
			onlineNews[news_id]++;
		}
	});
	io.emit("ONLINE_READS", onlineNews);
}

io.on("connection", socket => {
	socket.on('disconnect', () => {
		delete sockets[socket.id];
		refreshOnlineNews();
	});
	socket.on('read news', (news_id) => {
		sockets[socket.id] = news_id;
		refreshOnlineNews();
	});
});

var Redis = require('ioredis');
var redis = new Redis();
redis.subscribe(['system']);
redis.on('message', (channel, json) => {
	try {
		data = JSON.parse(json);
		if (typeof data.type !== 'undefined') {
			switch (data.type) {
				case 'COUNT_READS':
					io.emit("COUNT_READS", data.data);
					break;
			}
		}
	}
	catch (error) {
		console.log(error);
	}
})

server.listen(3001, () => {
	console.log('Server run on port: 3001...');
});