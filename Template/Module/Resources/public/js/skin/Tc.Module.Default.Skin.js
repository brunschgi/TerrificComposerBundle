(function($) {
    /**
     * SkinName Skin implementation for module Default.
     *
     * @author Your Name
     * @namespace Tc.Module.Default
     * @class SkinName
     * @extends Tc.Module
     * @constructor
     */
    Tc.Module.Default.SkinName = function(parent) {
        /** 
         * override the appropriate methods from the decorated module (ie. this.get = function()).
         * the former/original method may be called via parent.<method>()
         */
        this.on = function(callback) {
            // calling parent method
            parent.on(callback);
        };

        this.after = function() {
            // calling parent method
            parent.after();
        };
    };
})(Tc.$);
