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

    setValueByPathIn: function(obj, path, value, shouldOverride) {
      var i, len, key, isLast;

      path = path.split('.');
      len = path.length;

      for (i = 0; i < len; i++) {
        key = path[i];
        isLast = i === len - 1;

        if (obj.hasOwnProperty(key)) {
          
          if (typeof obj[key] !== 'object') {
            if (shouldOverride) {
              obj[key] = isLast ? value : {};
            } else {
              throw 'Can\'t override existing prop "' + path.slice(0, i + 1).join('.');
            }
          }

          obj = obj[key];
          
        } else if (isLast) {
          obj[key] = value || null;
        } else {
          obj[key] = {};
          obj = obj[key];
        }
      }
    }
  }
});