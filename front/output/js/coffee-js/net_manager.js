(function() {
  var CusPromise, root;

  root = typeof exports !== "undefined" && exports !== null ? exports : this;

  CusPromise = function(promise_) {
    var obj;
    obj = {
      to: function(target, attr, callback) {
        this.promise.then(function(data) {
          if (data.data !== void 0 && data.result !== void 0 && data.msg !== void 0) {
            target[attr] = data.data;
            return callback && callback.call(this, target[attr], data.result, data.msg);
          } else {
            target[attr] = data;
            return callback && callback.call(this, target[attr]);
          }
        });
        return promise_;
      },
      then: function(callback) {
        return this.promise.then(callback);
      },
      promise: promise_
    };
    return obj;
  };

  ng_app.factory("NetManager", [
    '$q', '$http', 'SERVER_HOST', '$timeout', function($q, $http, SERVER_HOST, $timeout) {
      var net;
      net = {
        get: function(url, params_, flag) {
          var base, counter, deferred, try_connect;
          deferred = $q.defer();
          base = SERVER_HOST;
          counter = 0;
          try_connect = function() {
            return $http.get(base + url, {
              params: params_
            }).success(function(data) {
              return deferred.resolve(data);
            }).error(function(data) {
              if (counter > 4) {
                return alert('操作失败！');
              } else {
                counter++;
                return $timeout(try_connect, counter * 500);
              }
            });
          };
          try_connect();
          return CusPromise(deferred.promise);
        },
        post: function(url, data_, flag) {
          var base, counter, deferred, try_connect;
          deferred = $q.defer();
          base = SERVER_HOST;
          counter = 0;
          try_connect = function() {
            return $http.post(base + url, data_).success(function(data) {
              return deferred.resolve(data);
            }).error(function(data) {
              if (counter > 4) {
                return alert('操作失败！');
              } else {
                counter++;
                return $timeout(try_connect, counter * 500);
              }
            });
          };
          try_connect();
          return deferred.promise;
        }
      };
      return net;
    }
  ]);

}).call(this);
