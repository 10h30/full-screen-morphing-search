/**
 * Main JavaScript file.
 *
 * Handles the effect for any WordPress search input that morphs into a fullscreen overlay. Renders Customizer choices.
 *
 * @author LebCit.
 * @since  3.3
 */

'use strict';

// Target any search input containing any default [attribute=value] of get_search_form().
let searchInputs = document.querySelectorAll('input[type=search], input[class=search-field], input[name=s], input[id=s]');

// Display the Full Screen search when The user focuses on a search field.
const morphSearch = document.getElementById('morphsearch');
const morphSearchInputField = document.getElementById('morphsearch-input');
for (const searchInput of searchInputs) {
	searchInput.addEventListener('click', () => {
		morphSearch.classList.add('open');
		// Focus on the Full Screen Morphing Search Input Field.
		setTimeout(function () {
			morphSearchInputField.focus();
		}, 500);
	});
}

// Hide the Morphing Search Page when the user clicks the close span.
const morphSearchCloseSpan = document.querySelector('.morphsearch-close');
morphSearchCloseSpan.addEventListener('click', () => {
	for (const searchInput of searchInputs) {
		searchInput.blur();
	}
	morphSearch.classList.remove('open');
});

// Hide the Morphing Search Page when the user press on the Escape key.
morphSearch.addEventListener('keydown', function (event) {
	if (event.key === 'Escape') {
		for (const searchInput of searchInputs) {
			searchInput.blur();
		}
		morphSearch.classList.remove('open');
	}
});

let fsmspBackgrounds = document.querySelectorAll('#morphsearch, div.morphsearch-content'),
	fsmspHeadings = document.querySelectorAll('div.dummy-column h2'),
	fsmspColumns = document.querySelectorAll('div.dummy-media-object'),
	fsmspLinks = document.querySelectorAll('div.dummy-media-object h3 a');

// Output default options' values !
