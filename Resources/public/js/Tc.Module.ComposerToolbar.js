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

            $('.create, .open', $ctx).on('click', function () {
                var $modal = $('.composerModal'),
                    $loader = $('.composerLoader'),
                    url = $(this).attr('href');

                $modal.addClass('intermediate');
                $loader.show();

                $modal.find('.dialog').load(url, function () {
                    that.sandbox.addModules($modal);
                    $loader.hide();
                    $modal.addClass('active');
                });

                return false;
            });

            $('.look', $ctx).on('click', function () {
                var $item = $(this).closest('li');

                if ($item.hasClass('active')) {
                    // disable look mode
                    $item.removeClass('active');
                    $('.composerModule').remove();
                }
                else {
                    // enable look mode
                    $item.addClass('active');

                    // show overlay over all modules on the page
                    $('.mod:not(.modComposerToolbar, .modComposerDialog):visible').each(function () {
                        var $this = $(this),
                            position = $this.offset(),
                            dimension = { height:$this.outerHeight() - 2, width:$this.outerWidth() - 2 },
                            positioning = $this.css('position'),
                            classes = $this.attr('class').split(' '),
                            name = '';

                        if (classes.length > 1) {
                            for (var i = 0, len = classes.length; i < len; i++) {
                                var part = $.trim(classes[i]);

                                if (part.indexOf('mod') === 0 && part.length > 3) {
                                    name = part.substr(3);
                                }
                            }
                        }

                        if (positioning == 'static') {
                            positioning = 'absolute';
                        }

                        var $overlay = $('<a href="/app_dev.php/terrific/composer/module/details/' + name + '/' + $this.data('composer-template').replace('/', ':') + '" class="composerModule"><span>' + name + '</span></a>').css({'zIndex':($this.css('zIndex') + 1), 'position':positioning, 'width':dimension.width, 'height':dimension.height, 'top':position.top, 'left':position.left});
                        $('body').append($overlay);
                    });
                }
                return false;
            });

            $('.composerModal a[href="#close"]').on('click', function () {
                var modules = [];

                $('.mod', $('.composerModal')).each(function () {
                    modules.push(that.sandbox.getModuleById($(this).data('id')));
                });

                $(this).closest('.modal').removeClass('active intermediate');
                that.sandbox.removeModules(modules);
                return false;
            })
        }
    });
})(Tc.$);
