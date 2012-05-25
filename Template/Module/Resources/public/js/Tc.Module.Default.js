(function($) {
    /**
     * Default module implementation.
     *
     * @author Your Name
     * @namespace Tc.Module
     * @class Default
     * @extends Tc.Module
     */
    Tc.Module.Default = Tc.Module.extend({
		/**
		 * Initializes the Default module.
		 * 
		 * @method init
		 * @return {void}
	 	 * @constructor
	     * @param {jQuery} $ctx the jquery context
	     * @param {Sandbox} sandbox the sandbox to get the resources from
	     * @param {String} modId the unique module id
		 */
		init: function($ctx, sandbox, modId) {
	      	// call base constructor
	        this._super($ctx, sandbox, modId);
	    },

        /**
         * Hook function to do all of your module stuff.
         *
         * @method on
         * @param {Function} callback function
         * @return void
         */
        on: function(callback) {
            callback();
        },
        
        /**
         * Hook function to trigger your events.
         *
         * @method after
         * @return void
         */
        after: function() {
        
        }
    });
})(Tc.$);
