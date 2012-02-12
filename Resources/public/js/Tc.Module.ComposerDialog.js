(function ($) {
    /**
     * ComposerDialog module implementation.
     *
     * @author Remo Brunschwiler
     * @namespace Tc.Module
     * @class ComposerDialog
     * @extends Tc.Module
     */
    Tc.Module.ComposerDialog = Tc.Module.extend({

        /**
         * Initializes the ComposerDialog module.
         *
         * @method init
         * @return {void}
         * @constructor
         * @param {jQuery} $ctx the jquery context
         * @param {Sandbox} sandbox the sandbox to get the resources from
         * @param {String} modId the unique module id
         */
        init:function ($ctx, sandbox, modId) {
            // call base constructor
            this._super($ctx, sandbox, modId);
        },

        /**
         * Hook function to bind the module specific events.
         *
         * @method onBinding
         * @return void
         */
        onBinding:function () {
            var $ctx = this.$ctx,
                that = this;

            // sidebar
            $('.sidebar a', $ctx).on('click', function() {
                var url =  $(this).attr('href');
                that.loadView(url);
                return false;
            });

            // create dialog options
            $('form.serialize', $ctx).on('submit', function() {
                var url =  $(this).attr('action');
                that.loadView(url, $(this).serializeArray());
                return false;
            });

            $('.addSkin', $ctx).on('click', function() {
                var $form = $('form', $ctx),
                    url =  $form.attr('action'),
                    data = $form.serializeArray();
                    data.push({ 'name' : 'addskin', 'value' : true });

                that.loadView(url, data);
                return false;
            });

            // search in open dialog
            var searchTimeout;
            var $list = $('.results li', $ctx);
            var $search = $('.search', $ctx);
            $search.on('keyup', function() {
                var $search = $(this);
                // check if the search array is already initialized

                // clear timeout if existing
                if(searchTimeout) {
                    clearTimeout(searchTimeout);
                }

                searchTimeout = setTimeout(function() {
                    var term = $search.val().replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&"),
                        matcher = new RegExp(term, "i" ),
                        results = $.grep( $list, function(item) {
                            return matcher.test($(item).data('search'));
                        });

                    $list.hide();
                    for(var i = 0, len = results.length; i < len; i++) {
                        $(results[i]).show();
                    }
                }, 250);
            });

            // open the first search result on submit
            $search.closest('form').on('submit', function() {
                window.location = $('.results li:visible:eq(0) a', $ctx).attr('href');
                return false;
            });


            // improve all select boxes
            $('select').chosen();
        },

        loadView : function(url, data) {
            var that = this,
                $ctx = this.$ctx,
                $modal = $('.composerModal'),
                $loader = $('.loader', $ctx);

            $loader.show();

            if(data) {
                $.post(url, data, function(data) {
                    var mod = that.sandbox.getModuleById(that.modId);
                    that.sandbox.removeModules([ mod ]);

                    $modal.find('.dialog').html(data);
                    $loader.hide();
                    $modal.addClass('active');
                    that.sandbox.addModules($modal);
                    $modal.find('input[type=text]:eq(0)').focus();
                });
            } else {
                $.get(url, function(data) {
                    var mod = that.sandbox.getModuleById(that.modId);
                    that.sandbox.removeModules([ mod ]);

                    $modal.find('.dialog').html(data);
                    $loader.hide();
                    $modal.addClass('active');
                    that.sandbox.addModules($modal);
                    $modal.find('input[type=text]:eq(0)').focus();
                });
            }
        }
    });
})(Tc.$);
