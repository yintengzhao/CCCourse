(function() {
  (function($) {
    return $.QueryString = function(a) {
      var b, i, j, p, ref;
      a = a.split('?')[1].split('&');
      if (a === "") {
        return {};
      }
      b = {};
      for (i = j = 0, ref = a.length; 0 <= ref ? j < ref : j > ref; i = 0 <= ref ? ++j : --j) {
        p = a[i].split('=');
        if (p.length !== 2) {
          continue;
        }
        b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
      }
      return b;
    };
  })(jQuery);

}).call(this);
