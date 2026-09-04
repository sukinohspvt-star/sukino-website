/**
 * Minimal, dependency-free JS: mobile menu toggle and scroll-to on
 * successful enquiry submission. Elementor widgets ship their own JS and
 * are unaffected by this file.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var toggle = document.querySelector( '.sukino-menu-toggle' );
		var nav = document.getElementById( 'site-navigation' );

		if ( toggle && nav ) {
			toggle.addEventListener( 'click', function () {
				var isOpen = nav.classList.toggle( 'is-open' );
				toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
			} );
		}

		if ( window.location.hash === '#sukino-enquiry-form' ) {
			var form = document.querySelector( '.sukino-ip-form' );
			if ( form ) {
				form.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			}
		}
	} );
} )();
