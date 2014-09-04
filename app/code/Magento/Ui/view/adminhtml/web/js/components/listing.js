define([
   '../lib/ko/scope',
    '../lib/rest/client',
    '../lib/rest/adapter/local',
    '../lib/mixin/resourceful',
    '_',
    'ko'
], function(Scope, RestClient, LocalAdapter, Resourceful, _, ko) {

    var ID_ATTRIBUTE = 'id';
    var DEFAULT_VIEW = 'grid';

    return Scope.extend({
        initialize: function(initial, config) {
            var paging;

            _.extend( this, initial );

            this.observe({
                rows:           initial.rows,
                fields:         initial.fields,
                checkedIds:     [],
                currentAction:  '',
                view:           DEFAULT_VIEW,
                isLocked:       false,
                massActions:    initial.massActions || []
            });

            this.observe({
                'meta.pages':       this.meta.pages,
                'meta.items':       this.meta.items,
                'paging.current':   this.paging.current,
                'paging.pageSize':  this.paging.pageSize
            });
            
            paging = this.paging;

            paging.current.subscribe( this.updatePaging.bind(this) );
            paging.pageSize.subscribe( this.updatePaging.bind(this) );

            this.params = {
                paging: {
                    current: paging.current(),
                    pageSize: paging.pageSize()
                }
            };

            var adapter = new LocalAdapter(config.resource);
            this.client = new RestClient(adapter);
        },

        applyMassAction: function (action) {
            if (this[action]) {
                this[action]();
            }    
        },

        toggleMassActions: function () {
            this.isMassActionVisible(!this.isMassActionVisible());
        },

        isAllVisibleSelected: function () {
            var checkedIds = this.checkedIds();
            var visibleIds = _.pluck(this.rows(), ID_ATTRIBUTE);

            var intersectedIds = _.intersection(checkedIds, visibleIds);
            var difference = _.difference(visibleIds, intersectedIds);
            
            return (checkedIds.length && visibleIds.length) && !difference.length;
        },

        _unselectOne: function(id) {
            var position = this.checkedIds.indexOf(id);

            if (position >= 0) {
                this.checkedIds.splice(position, 1);
            }
        },

        _unselect: function(ids) {
            _.each(ids, this._unselectOne, this);
        },

        _select: function(ids) {
            this.checkedIds(ids || []);
        },

        select: function(rows) {
            var toSelect = _.pluck(rows, ID_ATTRIBUTE);
            this._select(toSelect);
        },

        selectAll: function() {
            this.client
                .read(null)
                .then(function (result) { return result.rows })
                .done(this.select.bind(this));
        },

        unselectAll: function() {
            this._select([]);
        },

        unselectVisible: function() {
            return this.unselectAll();
        },

        selectVisible: function() {
            this.select(this.rows());
        },

        remove: function() {
            var idsToRemove = this.checkedIds();

            this.lock();
            this.client.remove(idsToRemove).done(function(removedIds) {
                this.reload()._unselect(removedIds);
            }.bind(this));
        },

        reload: function() {
            this.lock().client.read(this.params).done(function(result) {
                var meta = result.meta;

                this.unlock();

                this.rows(result.rows);

                this.meta.pages( meta.pages );
                this.meta.items( meta.items );

            }.bind(this));

            return this;
        },

        getCheckedQuantity: function() {
            return this.checkedIds().length;
        },

        isEmpty: function() {
            return !this.rows().length;
        },

        getViewTemplate: function() {
            return 'Magento_Ui.templates.listing.' + this.view();
        },

        setViewTo: function(type) {
            this.view(type);
        },

        setParams: function(params) {
            _.extend(this.params, params);

            return this;
        },

        updatePaging: function(){
            var paging = this.paging;

            this.setParams({
                paging: {
                    pageSize: paging.pageSize(),
                    current: paging.current()
                }
            }).reload();
        }
    }, Resourceful);
});