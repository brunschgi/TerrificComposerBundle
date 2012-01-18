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

            $('.sidebar a', $ctx).on('click', function() {
                var url =  $(this).attr('href');
                that.loadView(url);
                return false;
            });

            $('form', $ctx).on('submit', function() {
                var url =  $(this).attr('action');
                that.loadView(url, $(this).serializeArray());
                return false;
            });

            $('.addSkin', $ctx).on('click', function() {
                var url =  $(this).data('url');
                that.loadView(url, $('form', $ctx).serializeArray());
                return false;
            });
        },

        loadView : function(url, data) {
            var that = this,
                $ctx = this.$ctx,
                $modal = $('.composerModal'),
                $loader = $('.loader', $ctx);

            $modal.addClass('intermediate');
            $loader.show();

            if(data) {
                $.post(url, data, function(data) {
                    var mod = that.sandbox.getModuleById(that.modId);
                    that.sandbox.removeModules([ mod ]);

                    $modal.find('.dialog').html(data);
                    $loader.hide();
                    $modal.addClass('active');
                    that.sandbox.addModules($modal);
                });
            } else {
                $.get(url, function(data) {
                    var mod = that.sandbox.getModuleById(that.modId);
                    that.sandbox.removeModules([ mod ]);

                    $modal.find('.dialog').html(data);
                    $loader.hide();
                    $modal.addClass('active');
                    that.sandbox.addModules($modal);
                });
            }
        }
    });
})(Tc.$);
