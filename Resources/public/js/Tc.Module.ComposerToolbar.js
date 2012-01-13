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


        }
    });
})(Tc.$);
