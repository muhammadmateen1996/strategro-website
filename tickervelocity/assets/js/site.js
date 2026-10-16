/**
 * TickerVelocity front end. No dependencies.
 */
(function () {
	'use strict';

	var settings = window.tvSettings || { rest: '/wp-json/tv/v1/', refresh: 60, i18n: {} };
	var $ = function (sel, root) { return (root || document).querySelector(sel); };
	var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };
	var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* Header, menu and search -------------------------------------------------- */
	var header = $('[data-tv-header]');
	var toggle = function (button, panel, force) {
		var open = typeof force === 'boolean' ? force : button.getAttribute('aria-expanded') !== 'true';
		button.setAttribute('aria-expanded', open ? 'true' : 'false');
		panel.hidden = !open;
		return open;
	};
	var menuBtn = $('[data-tv-menu-toggle]');
	var searchBtn = $('[data-tv-search-toggle]');
	if (menuBtn) {
		menuBtn.addEventListener('click', function () {
			var open = toggle(menuBtn, $('#tv-mobile'));
			document.body.classList.toggle('tv-menu-open', open);
			if (searchBtn) { toggle(searchBtn, $('#tv-search'), false); }
		});
	}
	if (searchBtn) {
		searchBtn.addEventListener('click', function () {
			if (toggle(searchBtn, $('#tv-search'))) {
				$('#tv-search input[type="search"]').focus();
			}
		});
	}
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			if (searchBtn) { toggle(searchBtn, $('#tv-search'), false); }
			if (menuBtn) { toggle(menuBtn, $('#tv-mobile'), false); document.body.classList.remove('tv-menu-open'); }
		}
	});

	/* Scroll: compact header and reading progress ------------------------------ */
	var bar = $('.tv-progress span');
	var article = $('.tv-prose');
	var ticking = false;
	var onScroll = function () {
		ticking = false;
		if (header) { header.classList.toggle('is-scrolled', window.scrollY > 40); }
		if (bar && article) {
			var r = article.getBoundingClientRect();
			var p = Math.min(1, Math.max(0, (window.innerHeight * 0.4 - r.top) / r.height));
			bar.style.transform = 'scaleX(' + p.toFixed(3) + ')';
		}
		highlightToc();
	};
	window.addEventListener('scroll', function () {
		if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
	}, { passive: true });

	/* Table of contents ------------------------------------------------------- */
	var tocLinks = $$('.tv-toc a[href^="#"]');
	var headings = [];
	tocLinks.forEach(function (a) {
		var el = document.getElementById(decodeURIComponent(a.getAttribute('href').slice(1)));
		if (el && headings.indexOf(el) === -1) { headings.push(el); }
		a.addEventListener('click', function () {
			var d = a.closest('details');
			if (d) { d.open = false; }
		});
	});
	function highlightToc() {
		if (!headings.length) { return; }
		var current = headings[0].id;
		headings.forEach(function (h) {
			if (h.getBoundingClientRect().top < 150) { current = h.id; }
		});
		tocLinks.forEach(function (a) {
			a.classList.toggle('is-active', a.getAttribute('href') === '#' + current);
		});
	}
	onScroll();

	/* Market session (New York time) ------------------------------------------ */
	var session = $('[data-tv-session]');
	if (session && window.Intl) {
		var setSession = function () {
			var parts = {};
			new Intl.DateTimeFormat('en-US', { timeZone: 'America/New_York', weekday: 'short', hour: 'numeric', minute: 'numeric', hourCycle: 'h23' })
				.formatToParts(new Date()).forEach(function (p) { parts[p.type] = p.value; });
			var mins = Number(parts.hour) * 60 + Number(parts.minute);
			var weekend = parts.weekday === 'Sat' || parts.weekday === 'Sun';
			var state = 'closed';
			var label = 'US markets closed';
			if (!weekend && mins >= 570 && mins < 960) { state = 'open'; label = 'US markets open'; }
			else if (!weekend && mins >= 240 && mins < 570) { state = 'pre'; label = 'Pre-market'; }
			else if (!weekend && mins >= 960 && mins < 1200) { state = 'post'; label = 'After hours'; }
			session.setAttribute('data-state', state);
			$('[data-tv-session-label]', session).textContent = label;
			session.hidden = false;
		};
		setSession();
		setInterval(setSession, 60000);
	}

	/* Live prices: strip, tables and cards ---------------------------------- */
	var strip = $('[data-tv-strip]');
	var escape = function (s) {
		return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; });
	};
	var timeLabel = function (unix) {
		return unix ? new Date(unix * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
	};
	var setSpeed = function () {
		var track = strip && $('[data-tv-strip-track]', strip);
		if (track) {
			track.style.setProperty('--tv-strip-speed', Math.max(30, Math.round(track.scrollWidth / 2 / 45)) + 's');
		}
	};
	var updateCard = function (card, a) {
		var px = $('[data-tv-px]', card);
		var old = Number(px.getAttribute('data-value'));
		if (old && old !== a.value) {
			card.classList.remove('is-flash-up', 'is-flash-down');
			void card.offsetWidth; // Restart the flash animation.
			card.classList.add(a.value > old ? 'is-flash-up' : 'is-flash-down');
		}
		px.textContent = a.price;
		px.setAttribute('data-value', a.value);
		card.className = card.className.replace(/\btv-pc--(up|down|flat)\b/, 'tv-pc--' + a.dir);
		$('[data-tv-chg]', card).textContent = a.change;
		var svg = $('.tv-pc__spark', card);
		var range = $('.tv-pc__range', card);
		if (a.spark) {
			$('[data-tv-line]', svg).setAttribute('points', a.spark);
			$('[data-tv-area]', svg).setAttribute('points', '0,36 ' + a.spark + ' 120,36');
			svg.removeAttribute('hidden');
			range.hidden = true;
		} else if (a.range !== null && a.range !== undefined) {
			$('[data-tv-range]', range).style.left = Math.max(0, Math.min(100, a.range)) + '%';
			range.hidden = false;
		}
		$('[data-tv-low]', card).textContent = a.low ? 'L ' + a.low : '';
		$('[data-tv-high]', card).textContent = a.high ? 'H ' + a.high : '';
		$('[data-tv-span]', card).textContent = a.span || '';
	};
	var renderPrices = function (data) {
		if (!data || !data.assets || !data.assets.length) { return; }
		var rows = data.assets.map(function (i) {
			return '<li class="tv-quote tv-quote--' + escape(i.dir) + '"><span class="tv-quote__sym">' + escape(i.symbol) + '</span><span class="tv-quote__px">' + escape(i.price) + '</span><span class="tv-quote__chg">' + escape(i.change) + '</span></li>';
		}).join('');
		if (strip) {
			$$('.tv-strip__group', strip).forEach(function (g) { g.innerHTML = rows; });
			strip.hidden = false;
			setSpeed();
		}
		$$('[data-tv-markets-body]').forEach(function (body) {
			body.innerHTML = data.assets.slice(0, 10).map(function (i) {
				return '<tr class="tv-quote--' + escape(i.dir) + '"><th scope="row">' + escape(i.symbol) + '</th><td>' + escape(i.price) + '</td><td class="tv-markets__chg">' + escape(i.change) + '</td></tr>';
			}).join('');
		});
		var bySymbol = {};
		data.assets.forEach(function (a) { bySymbol[a.symbol] = a; });
		$$('[data-tv-asset]').forEach(function (card) {
			var a = bySymbol[card.getAttribute('data-tv-asset')];
			if (a) { updateCard(card, a); }
		});
		$$('[data-tv-strip-time]').forEach(function (el) { el.textContent = timeLabel(data.updated); });
	};
	var loadPrices = function () {
		if (document.hidden || !window.fetch) { return; }
		fetch(settings.rest + 'prices', { credentials: 'omit' })
			.then(function (r) { return r.ok ? r.json() : null; })
			.then(renderPrices)
			.catch(function () {});
	};
	setSpeed();
	if (strip || $('[data-tv-markets]') || $('[data-tv-asset]')) {
		loadPrices();
		setInterval(loadPrices, (settings.refresh || 60) * 1000);
	}

	/* Price board tabs */
	$$('[data-tv-board]').forEach(function (board) {
		var tabs = $$('[data-tv-filter]', board);
		tabs.forEach(function (tab) {
			tab.addEventListener('click', function () {
				var group = tab.getAttribute('data-tv-filter');
				tabs.forEach(function (t) {
					t.classList.toggle('is-active', t === tab);
					t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
				});
				$$('[data-tv-asset]', board).forEach(function (card) {
					card.hidden = !!group && card.getAttribute('data-group') !== group;
				});
			});
		});
	});

	/* Newsletter ---------------------------------------------------------------- */
	$$('[data-tv-newsletter]').forEach(function (form) {
		form.addEventListener('submit', function (e) {
			if (!window.fetch) { return; }
			e.preventDefault();
			var status = $('.tv-news__status', form);
			var button = $('button[type="submit"]', form);
			var data = new FormData(form);
			button.disabled = true;
			form.classList.add('is-busy');
			fetch(settings.rest + 'subscribe', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ email: data.get('email'), source: data.get('source'), company: data.get('company') })
			})
				.then(function (r) { return r.json().then(function (j) { return { ok: r.ok && j.ok, body: j }; }); })
				.then(function (res) {
					status.textContent = res.ok ? settings.i18n.subscribed : (res.body && res.body.message) || settings.i18n.error;
					form.classList.toggle('is-done', res.ok);
					if (res.ok) { form.reset(); }
				})
				.catch(function () { status.textContent = settings.i18n.error; })
				.then(function () { button.disabled = false; form.classList.remove('is-busy'); });
		});
	});
	$$('[data-tv-subscribe]').forEach(function (link) {
		link.addEventListener('click', function (e) {
			var target = $('.tv-news--band, .tv-news--page, .tv-news--inline, .tv-news--card, .tv-news--footer');
			if (!target) { return; }
			e.preventDefault();
			target.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' });
			var input = $('input[type="email"]', target);
			if (input) { setTimeout(function () { input.focus({ preventScroll: true }); }, reduce ? 0 : 500); }
			if (menuBtn) { toggle(menuBtn, $('#tv-mobile'), false); }
		});
	});

	/* Referral links: clickable only where the country check allows ---------- */
	var refs = $$('[data-tv-ref]');
	if (refs.length && window.fetch && window.atob) {
		fetch(settings.rest + 'geo', { credentials: 'omit', cache: 'no-store' })
			.then(function (r) { return r.ok ? r.json() : null; })
			.then(function (geo) {
				if (!geo || !geo.allowed) { return; }
				refs.forEach(function (span) {
					var a = document.createElement('a');
					a.className = 'tv-ref';
					a.href = window.atob(span.getAttribute('data-tv-ref'));
					a.rel = 'sponsored nofollow noopener';
					a.target = '_blank';
					a.innerHTML = span.innerHTML + '<span class="tv-ref__tag">' + (settings.i18n.ad || 'Ad') + '</span>';
					span.parentNode.replaceChild(a, span);
				});
				$$('[data-tv-refnote]').forEach(function (note) { note.hidden = false; });
			})
			.catch(function () {});
	}

	/* Copy link ------------------------------------------------------------------ */
	$$('[data-tv-copy]').forEach(function (button) {
		button.addEventListener('click', function () {
			var url = button.getAttribute('data-tv-copy');
			var done = function () {
				var label = $('span', button);
				var before = label.textContent;
				label.textContent = settings.i18n.copied;
				button.classList.add('is-copied');
				setTimeout(function () { label.textContent = before; button.classList.remove('is-copied'); }, 2000);
			};
			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(url).then(done, function () { window.prompt('', url); });
			} else {
				window.prompt('', url);
			}
		});
	});

	/* Reveal on scroll ------------------------------------------------------------ */
	var reveal = $$('.tv-reveal');
	$$('.tv-stagger').forEach(function (group) {
		Array.prototype.forEach.call(group.children, function (child, i) {
			child.classList.add('tv-reveal');
			child.style.setProperty('--tv-i', i);
			reveal.push(child);
		});
	});
	if (reduce || !('IntersectionObserver' in window)) {
		reveal.forEach(function (el) { el.classList.add('is-in'); });
	} else {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) { entry.target.classList.add('is-in'); io.unobserve(entry.target); }
			});
		}, { rootMargin: '0px 0px -8% 0px' });
		reveal.forEach(function (el) { io.observe(el); });
	}
	document.documentElement.classList.add('tv-ready');
})();
