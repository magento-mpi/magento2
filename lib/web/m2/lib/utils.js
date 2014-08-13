define(function () {
  return {
    getValueByPathIn: function(obj, path) {
      var found = obj,
        len, i, key;

      path = path.split('.');
      len = path.length;

      for (i = 0; i < len; i++) {
        key = path[i];

        if (found.hasOwnProperty(key)) {
          found = found[key];
        } else {
          return;
        }
      }

      return found;
    },

    setValueByPathIn: function(obj, path, value) {
      var i, len, key, last, isLast;
      
      path = path.split('.');
      len = path.length;
      last = len - 1;

      for (i = 0; i < len; i++) {
        key = path[i];
        isLast = i === last;

        if (!isLast) {
          if (!obj.hasOwnProperty(key)) {
            obj[key] = {};
          }

          obj = obj[key];  
        } else {
          obj[key] = value;
        }
        
      }
    }
  }
});