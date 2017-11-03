'use strict';

jQuery(function ($) {
    $(document).on('click', '.bd_tabbed__nav a', function (e) {
        var $this = $(this);
        var $container = $this.closest('.bd_tabbed');
        //Nav
        $this.closest('ul').find('.active').removeClass('active');
        $this.parent().addClass('active');
        //Contgent
        $container.find('.tab-pane').removeClass('active');
        var id = $this.attr('href');
        $(id).addClass('active');


        e.preventDefault();
    });
});