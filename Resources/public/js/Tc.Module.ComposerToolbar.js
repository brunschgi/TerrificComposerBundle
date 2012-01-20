(function ($) {
    /**
     * ComposerToolbar module implementation.
     *
     * @author Remo Brunschwiler
     * @namespace Tc.Module
     * @class ComposerToolbar
     * @extends Tc.Module
     */
    Tc.Module.ComposerToolbar = Tc.Module.extend({

        /**
         * Initializes the ComposerToolbar module.
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

            $('.create, .open', $ctx).on('click', function() {
                var $modal = $('.composerModal'),
                    $loader = $('.composerLoader'),
                    url = $(this).attr('href');

                $modal.addClass('intermediate');
                $loader.show();

                $modal.find('.dialog').load(url, function() {
                    that.sandbox.addModules($modal);
                    $loader.hide();
                    $modal.addClass('active');
                });

                return false;
            });

            $('.composerModal a[href="#close"]').on('click', function() {
                var modules = [];

                $('.mod',  $('.composerModal')).each(function() {
                    modules.push(that.sandbox.getModuleById($(this).data('id')));
                });

                $(this).closest('.modal').removeClass('active intermediate');
                that.sandbox.removeModules(modules);
                return false;
            })
        }
    });
})(Tc.$);
