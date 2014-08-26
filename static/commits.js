// forks & stargazers
var stargazers = document.querySelector('.github-stargazers');
var user = 'muaz-khan',
    repo = 'WebRTC-Experiment';

function addCommas(n) {
    return String(n).replace(/(\d)(?=(\d{3})+$)/g, '$1,')
}

function jsonp(path, callback) {
    var script = document.createElement('script');
    script.src = path + '?callback=callback00';
    script.async = true;
    if(callback) script.onload = callback;
    document.body.appendChild(script);
}

function callback00(obj) {
    if (gType == 'watch') {
        counter.innerHTML = addCommas(obj.data.watchers);
        console.log('watchers', obj.data.watchers);
    } else if (gType == 'fork') {
        counter.innerHTML = addCommas(obj.data.forks);
        console.log('forks', obj.data.forks);
    } else if (gType == 'follow') {
        counter.innerHTML = addCommas(obj.data.followers);
        console.log('followers', obj.data.followers);
    }

    counter.style.display = 'block';
}

var mainButton, counter, text, button;

function gitinfo(type, callback) {
    window.gType = type;

    mainButton = document.createElement('span');
    mainButton.className = 'github-btn';

    button = document.createElement('a');
    button.target = '_blank';
    button.className = 'gh-btn';
    mainButton.appendChild(button);

    var ghico = document.createElement('span');
    ghico.className = 'gh-ico';
    button.appendChild(ghico);

    text = document.createElement('span');
    text.className = 'gh-text';
    button.appendChild(text);

    counter = document.createElement('a');
    counter.target = '_blank';
    counter.className = 'gh-count';
	counter.innerHTML = '+1K';
    mainButton.appendChild(counter);

    if(stargazers) stargazers.appendChild(mainButton);

    // Set href to be URL for repo
    button.href = 'https://github.com/' + user + '/' + repo + '/';

    // Add the class, change the text label, set count link href
    if (gType == 'watch') {
        mainButton.className += ' github-watchers';
        text.innerHTML = 'Star ';
        counter.href = 'https://github.com/' + user + '/' + repo + '/stargazers';
    } else if (gType == 'fork') {
        mainButton.className += ' github-forks';
        text.innerHTML = ' Fork ';
        counter.href = 'https://github.com/' + user + '/' + repo + '/network';
    } else if (gType == 'follow') {
        mainButton.className += ' github-me';
        text.innerHTML = 'Follow @' + user;
        button.href = 'https://github.com/' + user;
        counter.href = 'https://github.com/' + user + '/followers';
    }

    // mainButton.className += ' github-btn-large';

    if (gType == 'follow') {
        jsonp('https://api.github.com/users/' + user, callback);
    } else {
        jsonp('https://api.github.com/repos/' + user + '/' + repo, callback);
    }
}

gitinfo('watch', function () {
	var callback;
	if(githubCommits) callback = getLatestCommits;
	else if(githubIssues) callback = getLatestIssues;
	
    gitinfo('fork', callback);
});

// issues
var githubIssues = document.getElementById('github-issues');
if(githubIssues) githubIssues.innerHTML = '<div style="padding:1em .8em;">Getting latest issues...</div>';

function issuesCallback(data) {
    githubIssues.innerHTML = '';
    data = data.data;
    var length = data.length;
    if (length > 10) length = 10;
    for (var i = 0; i < length; i++) {
        var issue = data[i];
        var div = document.createElement('div');
        div.className = 'commit';

        var header = issue.title;
        if (header.length > 50) {
            header = header.substr(0, 49) + '...';
            header = '<h2 title="' + issue.title + '">' + header + '</h2><br />';
        } else header = '<h2>' + issue.title + '</h2><br />';

        var message = issue.body;
        
        message = message.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        // message = message.replace(/https?:\/\/.*\.(?:png|jpg|jpeg|gif|PNG|JPG|JPEG|GIF)/g,'<img src="$&"/>');
        message = message.replace(urlRegex, shortenUrl).replace(/\n/g, '<br />');
        message = message.replace(/\n/g, '<br />');
        message = header + message;

        message = replaceMarkup(message);

        // if (message.length > 250) message = message.substr(0, 249) + '...';

        var commitDesc = document.createElement('div');
        commitDesc.className = 'commit-desc';
        commitDesc.innerHTML = message;
        div.appendChild(commitDesc);

        var commitMeta = document.createElement('div');
        commitMeta.className = 'commit-meta';

        var commitURL = document.createElement('a');
        commitURL.target = '_blank';
        commitURL.href = issue.html_url;
        commitURL.className = 'commit-url';
        commitURL.innerHTML = issue.comments + ' Comments (Submitted ' + timeDifference(new Date(), new Date(issue.created_at)) + ')';

        commitMeta.appendChild(commitURL);

        var authorship = document.createElement('div');
        authorship.className = 'authorship';

        var image = new Image(24, 24);
        image.className = 'gravatar';
        if(issue.user && issue.user.avatar_url) image.src = issue.user.avatar_url;
        authorship.appendChild(image);

        var span = document.createElement('span');
        span.className = 'author-name';
        span.innerHTML = '<a href="' + issue.user.html_url + '" rel="author" target="_blank">' + issue.user.login + '</a>';
        authorship.appendChild(span);

        commitMeta.appendChild(authorship);
        div.appendChild(commitMeta);

        if(githubIssues) githubIssues.appendChild(div);
    }
};

function getLatestIssues() {
    var script = document.createElement('script');
    script.src = 'https://api.github.com/repos/muaz-khan/WebRTC-Experiment/issues?sha=master&callback=issuesCallback';
    script.async = true;
    document.body.appendChild(script);
}

var githubCommits = document.getElementById('github-commits');
if(githubCommits) githubCommits.innerHTML = '<div style="padding:1em .8em;">Getting latest commits...</div>';

function commitsCallback(data) {
    githubCommits.innerHTML = '';

    data = data.data;

    var length = data.length;
    if (length > 15) length = 15;
    for (var i = 0; i < length; i++) {
        var commit = data[i];
        var div = document.createElement('div');
        div.className = 'commit';

        var message = commit.commit.message;
        message = message.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        // message = message.replace(/https?:\/\/.*\.(?:png|jpg|jpeg|gif|PNG|JPG|JPEG|GIF)/i,'<img src="$&">');
        message = message.replace(urlRegex, shortenUrl).replace(/\n/g, '<br />');
        message = message.replace(/\n/g, '<br />');

        message = replaceMarkup(message);

        var commitDesc = document.createElement('div');
        commitDesc.className = 'commit-desc';
        commitDesc.innerHTML = message;
        div.appendChild(commitDesc);

        var commitMeta = document.createElement('div');
        commitMeta.className = 'commit-meta';

        var commitURL = document.createElement('a');
        commitURL.target = '_blank';
        commitURL.href = commit.html_url;
        commitURL.className = 'commit-url';
        commitURL.innerHTML = timeDifference(new Date(), new Date(commit.commit.committer.date));
        commitMeta.appendChild(commitURL);

        var authorship = document.createElement('div');
        authorship.className = 'authorship';
		
		if(!commit.author) commit.author = 'muaz-khan';

        var image = new Image(24, 24);
        image.className = 'gravatar';
        image.src = commit.author.avatar_url;
		if(!commit.author || !commit.author.avatar_url) image.src = 'https://goo.gl/KaFpuL';
        authorship.appendChild(image);

        var span = document.createElement('span');
        span.className = 'author-name';
        span.innerHTML = '<a href="' + (commit.author.html_url || 'https://github.com/muaz-khan') + '" rel="author" target="_blank">' + (commit.author.login || 'Muaz Khan') + '</a>';
        authorship.appendChild(span);

        commitMeta.appendChild(authorship);
        div.appendChild(commitMeta);

        if(githubCommits) githubCommits.appendChild(div);
    }
};

function getLatestCommits() {
    var script2 = document.createElement('script');
    script2.src = 'https://api.github.com/repos/muaz-khan/WebRTC-Experiment/commits?sha=master&callback=commitsCallback';
    script2.async = true;
	
	var callback;
	if(githubIssues) callback = getLatestIssues;
	
	if(callback) script2.onload = callback;
    document.body.appendChild(script2);
}

var shortenUrl = function (url, protocol, host, port, path, filename, ext, query, fragment) {
	if(url == 'Socket.io/WebScockets—RTCMultiConnection-v1.4—RecordRTC') return 'Socket.io / WebScockets / RTCMultiConnection-v1.4 / RecordRTC';
	if(url == 'DataChannel.js') {
		return '<a href="https://github.com/muaz-khan/WebRTC-Experiment/tree/master/DataChannel" target="_blank"><strong>'+ url +'</strong></a>';
	}
	
	if(url == 'RTCMultiConnection.js' || url == 'RTCMultiConnection-v1.5.js' || url == 'RTCMultiConnection-v1.4.js' || url == 'RTCMultiConnection-v1.3.js' || url == 'RTCMultiConnection-v1.6.js') {
		return '<a href="http://www.rtcmulticonnection.org/docs/" target="_blank"><strong>'+ url +'</strong></a>';
	}
	
	if(url == 'socket.io' || url == 'node.js') {
		return '<a href="https://github.com/muaz-khan/WebRTC-Experiment/tree/master/socketio-over-nodejs" target="_blank"><strong>'+ url +'</strong></a>';
	}
	
	if(url == 'screen.js') {
		return '<a href="https://github.com/muaz-khan/WebRTC-Experiment/tree/master/screen-sharing" target="_blank"><strong>'+ url +'</strong></a>';
	}
	
	if(url == 'meeting.js') {
		return '<a href="https://github.com/muaz-khan/WebRTC-Experiment/tree/master/meeting" target="_blank"><strong>'+ url +'</strong></a>';
	}
	
	if(url == 'RecordRTC') {
		return '<a href="https://github.com/muaz-khan/WebRTC-Experiment/tree/master/RecordRTC" target="_blank"><strong>'+ url +'</strong></a>';
	}
	
    var limit = 18;
    if (url.charAt(0) == '(' && url.charAt(url.length - 1) == ')') {
        url = url.slice(1, -1);
    }
    if (!protocol) {
        url = 'http://' + url;
    }
    var domain = host.replace(/www\./gi, '');
    var visibleUrl = domain + (path || '') + (filename || '') + (ext || '') + (query || '') + (fragment || '');
    if (visibleUrl.length > limit && domain.length < limit) {
        visibleUrl = visibleUrl.slice(0, domain.length + (limit - domain.length)) + '...';
    }
    return '<a href="' + url + '" target="_blank">' + visibleUrl.replace('webrtc-experiment.com/', '/') + '</a>';
};

var urlRegex = /\(?\b(?:(http|https|ftp):\/\/)?((?:www.)?[a-zA-Z0-9\-\.]+[\.][a-zA-Z]{2,4})(?::(\d*))?(?=[\s\/,\.\)])([\/]{1}[^\s\?]*[\/]{1})*(?:\/?([^\s\n\?\[\]\{\}\#]*(?:(?=\.)){1}|[^\s\n\?\[\]\{\}\.\#]*)?([\.]{1}[^\s\?\#]*)?)?(?:\?{1}([^\s\n\#\[\]\(\)]*))?([\#][^\s\n]*)?\)?/gi;

function timeDifference(current, previous) {

    var msPerMinute = 60 * 1000;
    var msPerHour = msPerMinute * 60;
    var msPerDay = msPerHour * 24;
    var msPerMonth = msPerDay * 30;
    var msPerYear = msPerDay * 365;

    var elapsed = current - previous;

    if (elapsed < msPerMinute) {
        return Math.round(elapsed / 1000) + ' seconds ago';
    } else if (elapsed < msPerHour) {
        return Math.round(elapsed / msPerMinute) + ' minutes ago';
    } else if (elapsed < msPerDay) {
        return Math.round(elapsed / msPerHour) + ' hours ago';
    } else if (elapsed < msPerMonth) {
        return Math.round(elapsed / msPerDay) + ' days ago';
    } else if (elapsed < msPerYear) {
        return Math.round(elapsed / msPerMonth) + ' months ago';
    } else {
        return Math.round(elapsed / msPerYear) + ' years ago';
    }
}

function replaceMarkup(message) {
    message = message.replace(/```javascript([^```]+)```|```html([^```]+)```/g, '<pre>$1</pre>');
    message = message.replace(/```JavaScript([^```]+)```|```html([^```]+)```/g, '<pre>$1</pre>');
    message = message.replace(/```js([^```]+)```|```html([^```]+)```/g, '<pre>$1</pre>');
    message = message.replace(/```([^```]+)```/g, '<pre>$1</pre>');
    message = message.replace(/``([^``]+)``/g, '<pre>$1</pre>');
    message = message.replace(/`([^`]+)`/g, '<code>$1</code>');
    message = message.replace(/\*\*([^\*\*]+)\*\*/g, '<strong>$1</strong>');
    message = message.replace(/#([0-9]+)/g, '<a href="https://github.com/muaz-khan/WebRTC-Experiment/issues/$1" target="_blank">#$1</a>');
    
    message = message.replace(/```([^```]+)```/g, '<pre>$1</pre>');
    message = message.replace(/`([^`]+)`/g, '<code>$1</code>');

    return message;
}


function getCommonjs() {
	var script3 = document.createElement('script');
	script3.src = '//cdn.webrtc-experiment.com/common.js';
	script3.async = true;
	document.body.appendChild(script3);
}

getCommonjs();