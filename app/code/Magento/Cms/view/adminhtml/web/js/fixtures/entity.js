define(['m2/lib/ko/scope', 'storage', 'm2/lib/provider/model'], function (Scope, lo, DataProvider) {

  var initial = [
    {
      "id": "0",
      "title": "UPDATED Enable Cookies",
      "url": "enable-cookies",
      "layout": "1 column",
      "store_id": "All Store Views",
      "status": true,
      "created": 1407758413000,
      "modified": 1407758413000,
      "action": {
        "href": "/enable-cookies/?___store=default",
        "title": "Preview"
      }
    },
    {
      "id": "1",
      "title": "UPDATED Home page",
      "url": "home",
      "layout": "1 column",
      "store_id": "All Store Views",
      "status": true,
      "created": 1407848473000,
      "modified": 1407848473000,
      "action": {
        "href": "/home/?___store=default",
        "title": "Preview"
      }
    },
    {
      "id": "2",
      "title": "UPDATED 404 Not Found 1",
      "url": "no-route",
      "layout": "2 columns with right bar",
      "store_id": "All Store Views",
      "status": true,
      "created": 1407848473000,
      "modified": 1407848473000,
      "action": {
        "href": "/no-route/?___store=default",
        "title": "Preview"
      }
    },
    {
      "id": "3",
      "title": "UPDATED Privacy Policy",
      "url": "privacy-policy-cookie-restriction-mode",
      "layout": "1 column",
      "store_id": "All Store Views",
      "status": true,
      "created": 1407848473000,
      "modified": 1407848473000,
      "action": {
        "href": "/privacy-policy-cookie-restriction-mode/?___store=default",
        "title": "Preview"
      }
    },
    {
      "id": "4",
      "title": "NEW Another Page",
      "url": "privacy-policy-cookie-restriction-mode",
      "layout": "2 columns with right bar",
      "store_id": "All Store Views",
      "status": true,
      "created": 1407848473000,
      "modified": 1407848473000,
      "action": {
        "href": "/privacy-policy-cookie-restriction-mode/?___store=default",
        "title": "Preview"
      }
    },
    {
      "id": "5",
      "title": "NEW One more page",
      "url": "privacy-policy-cookie-restriction-mode",
      "layout": "1 column",
      "store_id": "All Store Views",
      "status": true,
      "created": 1407848473000,
      "modified": 1407848473000,
      "action": {
        "href": "/privacy-policy-cookie-restriction-mode/?___store=default",
        "title": "Preview"
      }
    },
    {
      "id": "6",
      "title": "NEW Aaaand another page",
      "url": "privacy-policy-cookie-restriction-mode",
      "layout": "1 column",
      "store_id": "All Store Views",
      "status": true,
      "created": 1407848473000,
      "modified": 1407848473000,
      "action": {
        "href": "/privacy-policy-cookie-restriction-mode/?___store=default",
        "title": "Preview"
      }
    }
  ];
  
  return Scope.extend({

    initialize: function () {
      this.def('listing', null);

      DataProvider.get('cms.pages.listing').done(this.listing.bind(this));
    },

    toggleListingView: function () {
      var listing = this.listing();

      if (listing) {
        listing.toggleView();
      }
    },

    reloadListing: function () {
      var listing = this.listing();

      if (listing) {
        listing.reload();
      }
    },

    empty: function () {
      lo.storage.empty();
    },

    populate: function () {
      lo.storage.set('cms.pages', initial);
    },

    addFewMore: function () {
      var additional = [
        {
          "id": "7",
          "title": "ADDITIONAL page",
          "url": "privacy-policy-cookie-restriction-mode",
          "layout": "1 column",
          "store_id": "All Store Views",
          "status": true,
          "created": 1407848473000,
          "modified": 1407848473000,
          "action": {
            "href": "/privacy-policy-cookie-restriction-mode/?___store=default",
            "title": "Preview"
          }
        },
        {
          "id": "8",
          "title": "another ADDITIONAL page",
          "url": "privacy-policy-cookie-restriction-mode",
          "layout": "1 column",
          "store_id": "All Store Views",
          "status": true,
          "created": 1407848473000,
          "modified": 1407848473000,
          "action": {
            "href": "/privacy-policy-cookie-restriction-mode/?___store=default",
            "title": "Preview"
          }
        }
      ]

      lo.storage.set('cms.pages', initial.concat(additional));
    }
  });
});