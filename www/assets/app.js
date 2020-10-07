jQuery(document).ready(function ($) {
    // Check height of breadcrumbs and increase height when necessary
    var $breadcrumbs = $('#intro.breadcrumbs');
    var count = $breadcrumbs.text().length;
    if (count > 155) {
        $breadcrumbs.css('height', '58px');
    }

    // Hide link to current site section in supplemental navigation menu
    $(
        '.supplemental-navigation .menu > ul > li.current_page_item > a:first, .supplemental-navigation .menu > ul > li.current_page_ancestor > a:first'
    ).addClass('offscreen-text');

    // IE7/8 supplemental navigation menu fix
    $('.supplemental-navigation .current_page_ancestor li:last-child').addClass(
        'last'
    );

    // Accordion Functionality

    // Add accordion-panel and collapsed classes when the accordion
    $('table.accordion tr:nth-child(2n)').addClass('accordion-panel collapsed');

    // For people with JS disabled, panels are hidden after JS is loaded, if the .accordion is present.
    $('table.accordion tr.accordion-panel + tr').hide();

    // Toggle visibility of accordion-panel + tr content also toggle between the collapsed and expanded class for styling
    $('table.accordion tr.accordion-panel').click(function () {
        $(this).next().toggle();
        $(this).toggleClass('collapsed expanded');
    });

    // Accordion formatting in rendered mark-up
    var $accordionPrimary = $('table.accordion tr.accordion-panel');

    // On hover, display tip
    $accordionPrimary.on({
        mouseenter: function () {
            var $this = $(this);
            if ($this.hasClass('collapsed')) {
                $this
                    .find('td:last')
                    .append('<span class="overlay"><p>↓</p></span>');
            } else if ($this.hasClass('expanded')) {
                $this
                    .find('td:last')
                    .append('<span class="overlay"><p>↑</p></span>');
            }
        },
        mouseleave: function () {
            $('.overlay').remove();
        },
        click: function () {
            var $this = $(this);
            if ($this.hasClass('expanded')) {
                $('.overlay p').text('↑');
            } else if ($this.hasClass('collapsed')) {
                $('.overlay p').text('↓');
            }
        },
    });

    // Add classes for IE fallback
    $('.profiles td:nth-child(1)').addClass('profiles-td-nth-child-1');
    $('.profiles td:nth-child(2)').addClass('profiles-td-nth-child-2');
    $('.profiles td:nth-child(3)').addClass('profiles-td-nth-child-3');

    $('.profiles td:nth-child(2) h6').addClass('profiles-inline-cells');
    $('.profiles td:nth-child(3) h6').addClass('profiles-inline-cells');
    $('.profiles td:nth-child(3) li').addClass('profiles-inline-cells');

    $('.profiles td:nth-child(3) *').addClass('profiles-td-nth-child-3-star');
    $('.profiles td li:nth-child(3)').addClass(
        'profiles-td-li-nth-child-3-after'
    );
    $('.profiles td li:nth-child(n+4)').addClass(
        '.profiles-td-li-nth-child-n-4'
    );

    $('.home #featured .slides').orbit({
        captions: true, // do you want captions?
        captionAnimation: 'fade', // fade, slideOpen, none
        captionAnimationSpeed: 800, // if so how quickly should they animate in
        bullets: false, // true or false to activate the bullet navigation
        bulletThumbs: true, // thumbnails for the bullets
        bulletThumbLocation: '../wp-content/themes/theme/images/thumbnails/', // location from this file where thumbs will be
        timer: false, // true or false to have the timer
    });
});
