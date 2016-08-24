# http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript#answer-3855394
(($)->
	$.QueryString = (a)-> 
		a = a.split('?')[1].split('&')
		if a == "" then return {};
		b = {}
		for i in [0...a.length]
			p = a[i].split('=');
			if p.length != 2
				continue
			b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
		return b;
)(jQuery);