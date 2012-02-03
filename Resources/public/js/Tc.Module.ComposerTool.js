(function ($) {
    /**
     * ComposerTool module implementation.
     *
     * @author Remo Brunschwiler
     * @namespace Tc.Module
     * @class ComposerTool
     * @extends Tc.Module
     */
    Tc.Module.ComposerTool = Tc.Module.extend({

        /**
         * Initializes the ComposerTool module.
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
                that = this,
                $template = $('.template', $ctx),
                $skins = $(".skins", $ctx);

            /* Module Configurator */
            $template.chosen().on('change', function() {
                var url = $(this).val(),
                    skins = $skins.val();

                if(skins) {
                    if($.isArray(skins)) {
                        skins = skins.join(',');
                    }

                    window.location = url + '/' + skins;
                }
                else {
                    window.location = url;
                }
            });

            $(".skins", $ctx).chosen().on('change', function() {
                var skins = $(this).val(),
                    url = $template.val();

                if(skins) {
                    if($.isArray(skins)) {
                        skins = skins.join(',');
                    }

                    window.location = url + '/' + skins;
                }
                else {
                    window.location = url;
                }
            });
        }
    });
})(Tc.$);
