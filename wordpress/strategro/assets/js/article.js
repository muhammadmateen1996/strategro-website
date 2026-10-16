/**
 * Article page: highlights the table-of-contents entry for the section
 * being read, counts down the minutes left, and copies the article link.
 * Works without the motion engine (and with reduced motion).
 */
(function () {
	'use strict';

	var article = document.querySelector('.sg-post');
	if (!article) {
		return;
	}
	var prose = article.querySelector('.sg-prose--article');

	/* Table of contents ---------------------------------------------------- */
	var links = Array.prototype.slice.call(document.querySelectorAll('.sg-toc a[href^="#"]'));
	var headings = [];
	links.forEach(function (link) {
		var target = document.getElementById(decodeURIComponent(link.getAttribute('href').slice(1)));
		if (target && headings.indexOf(target) === -1) {
			headings.push(target);
		}
	});

	var setActive = function (id) {
		links.forEach(function (link) {
			var on = link.getAttribute('href') === '#' + id;
			link.classList.toggle('is-active', on);
			if (on) {
				link.setAttribute('aria-current', 'true');
			} else {
				link.removeAttribute('aria-current');
			}
		});
	};

	// Inline (phone) contents closes once a section is picked.
	document.querySelectorAll('.sg-toc--inline a').forEach(function (link) {
		link.addEventListener('click', function () {
			var details = link.closest('details');
			if (details) {
				details.open = false;
			}
		});
	});

	/* Reading progress ----------------------------------------------------- */
	var minutes = Number(article.getAttribute('data-sg-minutes')) || 1;
	var left = document.querySelector('[data-sg-left]');
	var bar = document.querySelector('.sg-toc__bar span');
	var ticking = false;

	var update = function () {
		ticking = false;
		if (headings.length) {
			var current = headings[0].id;
			var line = 140;
			headings.forEach(function (heading) {
				if (heading.getBoundingClientRect().top - line <= 0) {
					current = heading.id;
				}
			});
			setActive(current);
		}
		if (!prose) {
			return;
		}
		var rect = prose.getBoundingClientRect();
		var progress = Math.min(1, Math.max(0, (window.innerHeight * 0.35 - rect.top) / rect.height));
		if (bar) {
			bar.style.transform = 'scaleX(' + progress.toFixed(3) + ')';
		}
		if (left) {
			var remaining = Math.ceil(minutes * (1 - progress));
			left.textContent = progress >= 0.98 || remaining <= 0 ? left.getAttribute('data-done') : remaining + ' ' + left.getAttribute('data-unit');
		}
	};

	window.addEventListener(
		'scroll',
		function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		},
		{ passive: true }
	);
	window.addEventListener('resize', update);
	update();

	/* Copy link ------------------------------------------------------------ */
	document.querySelectorAll('[data-sg-copy]').forEach(function (button) {
		button.addEventListener('click', function () {
			var url = button.getAttribute('data-sg-copy');
			var done = function () {
				var label = button.querySelector('.sg-share__label');
				var before = label ? label.textContent : '';
				button.classList.add('is-copied');
				if (label) {
					label.textContent = button.getAttribute('data-copied');
				}
				setTimeout(function () {
					button.classList.remove('is-copied');
					if (label) {
						label.textContent = before;
					}
				}, 2000);
			};
			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(url).then(done, function () {
					window.prompt('', url);
				});
			} else {
				window.prompt('', url);
			}
		});
	});
})();
