/**
 * Strategro motion engine.
 *
 * Effects are opt-in by CSS class, so they can be added to any Elementor
 * element from Advanced > CSS Classes:
 *
 *   sg-reveal    fade + rise + un-blur when scrolled into view
 *   sg-split     headline words slide up one by one
 *   sg-stagger   children of a container appear one after another
 *   sg-steps     a scroll-scrubbed sequence: .sg-line draws, .sg-step items rise
 *   sg-line      a hairline that draws itself across as you scroll
 *   sg-tilt      3D tilt with a light glare under the cursor
 *   sg-magnetic  button drifts toward the cursor
 *   sg-spot      soft light follows the cursor inside a section
 *   sg-parallax  drifts slower than the page (sg-parallax-fast: faster)
 *   sg-counter   numbers count up when they appear
 *   sg-network   animated signal network drawn behind a section
 *   sg-marquee   children scroll sideways forever
 *   sg-feed      cycles items of a "live activity" list
 *   sg-hscroll   pins the section and scrolls .sg-hscroll-track sideways (desktop)
 *   sg-d1 … sg-d6  delay a reveal by 0.1s steps
 *
 * Nothing animates inside the Elementor editor or for visitors who ask for
 * reduced motion; content is always visible without JavaScript.
 */
(function () {
	'use strict';

	var html = document.documentElement;
	var body = document.body;
	window.strategroMotionReady = true;

	var settings = window.strategroSettings || {};
	var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
	var inEditor = window.location.search.indexOf('elementor-preview') !== -1 || body.classList.contains('elementor-editor-active');

	function $$(selector, root) {
		return Array.prototype.slice.call((root || document).querySelectorAll(selector));
	}

	function once(fn) {
		var done = false;
		return function () {
			if (!done) {
				done = true;
				fn();
			}
		};
	}

	/* Header, mobile menu and progress bar work everywhere ------------------ */
	initHeader();
	initMobileMenu();
	initProgress();

	if (inEditor) {
		html.classList.remove('sg-motion');
		html.classList.add('sg-loaded');
		return;
	}

	var hasGsap = !!(window.gsap && window.ScrollTrigger);
	if (!hasGsap) {
		html.classList.add('sg-failed', 'sg-loaded');
		return;
	}

	if (reduce || !html.classList.contains('sg-motion')) {
		html.classList.add('sg-loaded');
		return;
	}

	var gsap = window.gsap;
	var ScrollTrigger = window.ScrollTrigger;
	gsap.registerPlugin(ScrollTrigger);

	initTransitions();

	var start = once(function () {
		html.classList.add('sg-loaded');
		// Let the loader begin fading before the first wave starts.
		setTimeout(initEffects, 120);
	});

	if (document.querySelector('.sg-loader') && document.fonts && document.fonts.ready) {
		document.fonts.ready.then(function () {
			setTimeout(start, 250);
		});
		setTimeout(start, 1400);
	} else {
		start();
	}

	window.addEventListener('load', function () {
		ScrollTrigger.refresh();
	});

	/* --------------------------------------------------------------------- */

	function initEffects() {
		initSplit();
		initReveal();
		initStagger();
		initSteps();
		initLines();
		initCounters();
		initParallax();
		initMarquee();
		initFeed();
		initHScroll();
		initNetwork();
		if (finePointer) {
			initTilt();
			initMagnetic();
			initSpot();
		}
		ScrollTrigger.refresh();
	}

	/** Elements visible on first load animate in reading order, not all at once. */
	function introDelay(el) {
		var top = el.getBoundingClientRect().top;
		if (top > window.innerHeight) {
			return 0;
		}
		introDelay.count = (introDelay.count || 0) + 1;
		return 0.15 + introDelay.count * 0.09;
	}

	function classDelay(el) {
		var match = el.className.match(/\bsg-d(\d)\b/);
		return match ? Number(match[1]) * 0.1 : 0;
	}

	/* Text ------------------------------------------------------------------ */
	function splitTarget(el) {
		return el.querySelector('.elementor-heading-title') || el;
	}

	function splitWords(root) {
		var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null);
		var nodes = [];
		while (walker.nextNode()) {
			nodes.push(walker.currentNode);
		}
		nodes.forEach(function (node) {
			if (!node.nodeValue.trim()) {
				return;
			}
			var gradient = node.parentElement && node.parentElement.closest('.sg-gradient-text');
			var frag = document.createDocumentFragment();
			node.nodeValue.split(/(\s+)/).forEach(function (part) {
				if (!part) {
					return;
				}
				if (/^\s+$/.test(part)) {
					frag.appendChild(document.createTextNode(part));
					return;
				}
				var outer = document.createElement('span');
				outer.className = 'sg-word';
				var inner = document.createElement('span');
				inner.textContent = part;
				if (gradient) {
					inner.className = 'sg-gradient-text';
				}
				outer.appendChild(inner);
				frag.appendChild(outer);
			});
			node.parentNode.replaceChild(frag, node);
		});
		// Gradient now lives on each word, so the wrapper must not repaint it.
		$$('.sg-gradient-text', root).forEach(function (span) {
			if (span.querySelector('.sg-word')) {
				span.classList.remove('sg-gradient-text');
			}
		});
		return $$('.sg-word > span', root);
	}

	function initSplit() {
		$$('.sg-split').forEach(function (el) {
			var words = splitWords(splitTarget(el));
			gsap.set(el, { opacity: 1 });
			gsap.set(words, { yPercent: 115, rotate: 3 });
			gsap.to(words, {
				yPercent: 0,
				rotate: 0,
				duration: 1.1,
				ease: 'expo.out',
				stagger: 0.055,
				delay: introDelay(el) + classDelay(el),
				scrollTrigger: { trigger: el, start: 'top 90%', once: true },
			});
		});
	}

	function initReveal() {
		$$('.sg-reveal').forEach(function (el) {
			gsap.fromTo(
				el,
				{ opacity: 0, y: 44, filter: 'blur(10px)' },
				{
					opacity: 1,
					y: 0,
					filter: 'blur(0px)',
					duration: 1.15,
					ease: 'expo.out',
					delay: introDelay(el) + classDelay(el),
					clearProps: 'filter',
					scrollTrigger: { trigger: el, start: 'top 90%', once: true },
				}
			);
		});
	}

	function childrenOf(el) {
		var inner = el.querySelector(':scope > .e-con-inner') || el;
		return Array.prototype.filter.call(inner.children, function (child) {
			return child.nodeType === 1 && !/^(SCRIPT|STYLE)$/.test(child.tagName);
		});
	}

	function initStagger() {
		$$('.sg-stagger').forEach(function (el) {
			var items = childrenOf(el);
			if (!items.length) {
				return;
			}
			gsap.fromTo(
				items,
				{ opacity: 0, y: 56, scale: 0.96 },
				{
					opacity: 1,
					y: 0,
					scale: 1,
					duration: 1,
					ease: 'expo.out',
					stagger: 0.1,
					delay: introDelay(el) + classDelay(el),
					scrollTrigger: { trigger: el, start: 'top 86%', once: true },
				}
			);
		});
	}

	function initSteps() {
		$$('.sg-steps').forEach(function (el) {
			var line = el.querySelector('.sg-line');
			var steps = $$('.sg-step', el);
			if (!steps.length) {
				return;
			}
			if (line) {
				line.classList.add('sg-line--owned');
			}
			var tl = gsap.timeline({
				scrollTrigger: { trigger: el, start: 'top 80%', end: 'top 30%', scrub: 0.8 },
			});
			if (line) {
				tl.fromTo(line, { scaleX: 0 }, { scaleX: 1, ease: 'none', duration: 1 });
			}
			tl.fromTo(
				steps,
				{ opacity: 0, y: 60, rotateX: -12 },
				{ opacity: 1, y: 0, rotateX: 0, stagger: 0.25, duration: 0.8, ease: 'power2.out' },
				line ? 0.15 : 0
			);
		});
	}

	function initLines() {
		$$('.sg-line').forEach(function (line) {
			if (line.classList.contains('sg-line--owned')) {
				return;
			}
			gsap.fromTo(
				line,
				{ scaleX: 0 },
				{ scaleX: 1, ease: 'none', scrollTrigger: { trigger: line, start: 'top 92%', end: 'top 55%', scrub: 0.6 } }
			);
		});
	}

	function initCounters() {
		$$('.sg-counter').forEach(function (el) {
			var target = splitTarget(el);
			var original = target.textContent;
			var numbers = original.match(/\d+/g);
			if (!numbers) {
				return;
			}
			var state = { p: 0 };
			var render = function () {
				var i = 0;
				target.textContent = original.replace(/\d+/g, function () {
					var value = Number(numbers[i++]);
					return String(Math.round(value * state.p));
				});
			};
			render();
			gsap.to(state, {
				p: 1,
				duration: 1.8,
				ease: 'power3.out',
				onUpdate: render,
				onComplete: function () {
					target.textContent = original;
				},
				scrollTrigger: { trigger: el, start: 'top 88%', once: true },
			});
		});
	}

	function initParallax() {
		$$('.sg-parallax, .sg-parallax-fast').forEach(function (el) {
			var amount = el.classList.contains('sg-parallax-fast') ? -22 : -10;
			gsap.fromTo(
				el,
				{ yPercent: -amount / 2 },
				{
					yPercent: amount,
					ease: 'none',
					scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true },
				}
			);
		});
	}

	/* Showcase sections ----------------------------------------------------- */
	function initMarquee() {
		$$('.sg-marquee').forEach(function (el) {
			var host = el.querySelector(':scope > .e-con-inner') || el;
			var items = childrenOf(el);
			if (!items.length || host.querySelector('.sg-marquee__track')) {
				return;
			}
			var viewport = document.createElement('div');
			viewport.className = 'sg-marquee__viewport';
			var track = document.createElement('div');
			track.className = 'sg-marquee__track';
			items.forEach(function (item) {
				track.appendChild(item);
			});
			items.forEach(function (item) {
				var clone = item.cloneNode(true);
				clone.setAttribute('aria-hidden', 'true');
				track.appendChild(clone);
			});
			viewport.appendChild(track);
			host.appendChild(viewport);
			var seconds = Math.max(24, Math.round(track.scrollWidth / 2 / 60));
			track.style.setProperty('--sg-marquee-speed', seconds + 's');
		});
	}

	function initFeed() {
		$$('.sg-feed').forEach(function (feed) {
			var list = feed.querySelector('.sg-feed__list');
			if (!list) {
				return;
			}
			var visible = Number(feed.getAttribute('data-visible')) || 4;
			var items = $$('li', list);
			if (items.length <= visible) {
				return;
			}
			var labels = ['just now', '1 min ago', '3 min ago', '6 min ago', '9 min ago', '14 min ago'];
			var stamp = function () {
				$$('li:not([hidden])', list).forEach(function (li, i) {
					var time = li.querySelector('time');
					if (time) {
						time.textContent = labels[Math.min(i, labels.length - 1)];
					}
				});
			};
			items.forEach(function (li, i) {
				li.hidden = i >= visible;
			});
			stamp();

			var next = visible;
			var tick = function () {
				var current = $$('li:not([hidden])', list);
				var incoming = items[next % items.length];
				next++;
				current.forEach(function (li) {
					li.classList.remove('is-new');
				});
				var outgoing = current[current.length - 1];
				list.insertBefore(incoming, list.firstChild);
				incoming.hidden = false;
				incoming.classList.add('is-new');
				gsap.fromTo(incoming, { opacity: 0, y: -18, scale: 0.97 }, { opacity: 1, y: 0, scale: 1, duration: 0.7, ease: 'expo.out' });
				if (outgoing && outgoing !== incoming) {
					outgoing.hidden = true;
					list.appendChild(outgoing);
				}
				stamp();
			};

			var timer = null;
			ScrollTrigger.create({
				trigger: feed,
				start: 'top bottom',
				end: 'bottom top',
				onToggle: function (self) {
					clearInterval(timer);
					if (self.isActive) {
						timer = setInterval(tick, 2600);
					}
				},
			});
		});
	}

	function initHScroll() {
		var mm = gsap.matchMedia();
		$$('.sg-hscroll').forEach(function (section) {
			var track = section.querySelector('.sg-hscroll-track');
			if (!track) {
				return;
			}
			// Pin a wrapper around the card row (not the whole section, whose
			// heading would push the cards below the fold on shorter screens),
			// and never transform the pinned element itself.
			var pinBox = document.createElement('div');
			pinBox.className = 'sg-hscroll-pin';
			track.parentNode.insertBefore(pinBox, track);
			pinBox.appendChild(track);
			mm.add('(min-width: 1024px)', function () {
				var distance = function () {
					return Math.max(0, track.scrollWidth - window.innerWidth);
				};
				if (distance() <= 0) {
					return;
				}
				var cards = childrenOf(track);
				var mapEnter = gsap.utils.mapRange(1.02, 0.72, 0, 1);
				// Cards fade and grow in as they travel in from the right edge.
				var updateCards = function () {
					var vw = window.innerWidth;
					cards.forEach(function (card) {
						var p = gsap.utils.clamp(0, 1, mapEnter(card.getBoundingClientRect().left / vw));
						gsap.set(card, { opacity: 0.3 + 0.7 * p, scale: 0.9 + 0.1 * p });
					});
				};
				var tween = gsap.to(track, {
					x: function () {
						return -distance();
					},
					ease: 'none',
					scrollTrigger: {
						trigger: pinBox,
						start: function () {
							// Centre the cards; if they're taller than the window, pin near the top.
							return pinBox.offsetHeight + 160 > window.innerHeight ? 'top top+=40' : 'center center';
						},
						end: function () {
							return '+=' + distance();
						},
						pin: pinBox,
						// Elementor containers are flex boxes, where GSAP turns pin
						// spacing off by default; without it the next section scrolls over the cards.
						pinSpacing: true,
						scrub: 0.8,
						anticipatePin: 1,
						invalidateOnRefresh: true,
					},
					onUpdate: updateCards,
				});
				updateCards();
				return function () {
					tween.scrollTrigger && tween.scrollTrigger.kill();
					tween.kill();
				};
			});
		});
	}

	/* Signal network canvas ------------------------------------------------- */
	function initNetwork() {
		$$('.sg-network').forEach(network);
	}

	function network(host) {
		var canvas = document.createElement('canvas');
		canvas.className = 'sg-network__canvas';
		canvas.setAttribute('aria-hidden', 'true');
		host.insertBefore(canvas, host.firstChild);

		var ctx = canvas.getContext('2d');
		var dpr = Math.min(window.devicePixelRatio || 1, 2);
		var width = 0;
		var height = 0;
		var nodes = [];
		var pulses = [];
		var mouse = { x: -9999, y: -9999 };
		var raf = null;
		var linkDistance = 140;

		function resize() {
			var rect = host.getBoundingClientRect();
			width = rect.width;
			height = rect.height;
			canvas.width = Math.round(width * dpr);
			canvas.height = Math.round(height * dpr);
			ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
			var count = Math.round(Math.min(120, Math.max(36, (width * height) / 13000)));
			nodes = [];
			for (var i = 0; i < count; i++) {
				nodes.push({
					x: Math.random() * width,
					y: Math.random() * height,
					vx: (Math.random() - 0.5) * 0.28,
					vy: (Math.random() - 0.5) * 0.28,
					hub: Math.random() < 0.09,
				});
			}
			pulses = [];
			for (var p = 0; p < 7; p++) {
				pulses.push({ from: Math.floor(Math.random() * count), to: -1, t: 0 });
			}
		}

		function nearest(index) {
			var a = nodes[index];
			var best = -1;
			var bestD = linkDistance;
			for (var i = 0; i < nodes.length; i++) {
				if (i === index) {
					continue;
				}
				var dx = nodes[i].x - a.x;
				var dy = nodes[i].y - a.y;
				var d = Math.sqrt(dx * dx + dy * dy);
				if (d < bestD && Math.random() > 0.35) {
					bestD = d;
					best = i;
				}
			}
			return best;
		}

		function frame() {
			ctx.clearRect(0, 0, width, height);
			var i;
			var j;
			for (i = 0; i < nodes.length; i++) {
				var n = nodes[i];
				n.x += n.vx;
				n.y += n.vy;
				if (n.x < 0 || n.x > width) {
					n.vx *= -1;
				}
				if (n.y < 0 || n.y > height) {
					n.vy *= -1;
				}
				var mx = mouse.x - n.x;
				var my = mouse.y - n.y;
				var md = Math.sqrt(mx * mx + my * my);
				if (md < 180 && md > 1) {
					n.x += (mx / md) * 0.35;
					n.y += (my / md) * 0.35;
				}
			}

			ctx.lineWidth = 1;
			for (i = 0; i < nodes.length; i++) {
				for (j = i + 1; j < nodes.length; j++) {
					var dx = nodes[i].x - nodes[j].x;
					var dy = nodes[i].y - nodes[j].y;
					var d = Math.sqrt(dx * dx + dy * dy);
					if (d < linkDistance) {
						var alpha = (1 - d / linkDistance) * 0.32;
						ctx.strokeStyle = nodes[i].hub || nodes[j].hub ? 'rgba(201,154,68,' + alpha + ')' : 'rgba(250,248,242,' + alpha * 0.45 + ')';
						ctx.beginPath();
						ctx.moveTo(nodes[i].x, nodes[i].y);
						ctx.lineTo(nodes[j].x, nodes[j].y);
						ctx.stroke();
					}
				}
				var cx = mouse.x - nodes[i].x;
				var cy = mouse.y - nodes[i].y;
				var cd = Math.sqrt(cx * cx + cy * cy);
				if (cd < 200) {
					ctx.strokeStyle = 'rgba(221,187,111,' + (1 - cd / 200) * 0.55 + ')';
					ctx.beginPath();
					ctx.moveTo(nodes[i].x, nodes[i].y);
					ctx.lineTo(mouse.x, mouse.y);
					ctx.stroke();
				}
			}

			for (i = 0; i < nodes.length; i++) {
				var node = nodes[i];
				ctx.beginPath();
				if (node.hub) {
					ctx.fillStyle = 'rgba(221,187,111,0.95)';
					ctx.shadowColor = 'rgba(221,187,111,0.9)';
					ctx.shadowBlur = 14;
					ctx.arc(node.x, node.y, 2.4, 0, Math.PI * 2);
				} else {
					ctx.fillStyle = 'rgba(250,248,242,0.38)';
					ctx.shadowBlur = 0;
					ctx.arc(node.x, node.y, 1.3, 0, Math.PI * 2);
				}
				ctx.fill();
			}
			ctx.shadowBlur = 0;

			// Signals travelling along the network.
			pulses.forEach(function (pulse) {
				if (pulse.to < 0) {
					pulse.to = nearest(pulse.from);
					pulse.t = 0;
					if (pulse.to < 0) {
						pulse.from = Math.floor(Math.random() * nodes.length);
						return;
					}
				}
				var a = nodes[pulse.from];
				var b = nodes[pulse.to];
				pulse.t += 0.022;
				var x = a.x + (b.x - a.x) * pulse.t;
				var y = a.y + (b.y - a.y) * pulse.t;
				var glow = ctx.createRadialGradient(x, y, 0, x, y, 10);
				glow.addColorStop(0, 'rgba(255,240,200,0.95)');
				glow.addColorStop(1, 'rgba(221,187,111,0)');
				ctx.fillStyle = glow;
				ctx.beginPath();
				ctx.arc(x, y, 10, 0, Math.PI * 2);
				ctx.fill();
				if (pulse.t >= 1) {
					pulse.from = pulse.to;
					pulse.to = -1;
				}
			});

			raf = window.requestAnimationFrame(frame);
		}

		function play() {
			if (!raf) {
				raf = window.requestAnimationFrame(frame);
			}
		}

		function pause() {
			if (raf) {
				window.cancelAnimationFrame(raf);
				raf = null;
			}
		}

		resize();
		new ResizeObserver(function () {
			resize();
		}).observe(host);
		new IntersectionObserver(function (entries) {
			entries[0].isIntersecting ? play() : pause();
		}).observe(host);

		if (finePointer) {
			host.addEventListener('mousemove', function (e) {
				var rect = host.getBoundingClientRect();
				mouse.x = e.clientX - rect.left;
				mouse.y = e.clientY - rect.top;
			});
			host.addEventListener('mouseleave', function () {
				mouse.x = -9999;
				mouse.y = -9999;
			});
		}
		gsap.fromTo(canvas, { opacity: 0 }, { opacity: 1, duration: 2, ease: 'power2.out' });
	}

	/* Pointer effects (mouse/trackpad only) -------------------------------- */
	function initTilt() {
		$$('.sg-tilt').forEach(function (el) {
			var rotX = gsap.quickTo(el, 'rotationX', { duration: 0.6, ease: 'power3.out' });
			var rotY = gsap.quickTo(el, 'rotationY', { duration: 0.6, ease: 'power3.out' });
			gsap.set(el, { transformPerspective: 1000 });
			el.addEventListener('mousemove', function (e) {
				var rect = el.getBoundingClientRect();
				var px = (e.clientX - rect.left) / rect.width;
				var py = (e.clientY - rect.top) / rect.height;
				rotY((px - 0.5) * 10);
				rotX((0.5 - py) * 10);
				el.style.setProperty('--sg-gx', px * 100 + '%');
				el.style.setProperty('--sg-gy', py * 100 + '%');
				el.style.setProperty('--sg-glare', '1');
			});
			el.addEventListener('mouseleave', function () {
				rotX(0);
				rotY(0);
				el.style.setProperty('--sg-glare', '0');
			});
		});
	}

	function initMagnetic() {
		$$('.sg-magnetic').forEach(function (el) {
			var target = el.querySelector('.elementor-button') || el;
			var moveX = gsap.quickTo(target, 'x', { duration: 0.5, ease: 'power3.out' });
			var moveY = gsap.quickTo(target, 'y', { duration: 0.5, ease: 'power3.out' });
			el.addEventListener('mousemove', function (e) {
				var rect = target.getBoundingClientRect();
				moveX((e.clientX - (rect.left + rect.width / 2)) * 0.3);
				moveY((e.clientY - (rect.top + rect.height / 2)) * 0.3);
			});
			el.addEventListener('mouseleave', function () {
				gsap.to(target, { x: 0, y: 0, duration: 0.9, ease: 'elastic.out(1, 0.4)' });
			});
		});
	}

	function initSpot() {
		$$('.sg-spot').forEach(function (el) {
			el.addEventListener('mousemove', function (e) {
				var rect = el.getBoundingClientRect();
				el.style.setProperty('--sg-mx', e.clientX - rect.left + 'px');
				el.style.setProperty('--sg-my', e.clientY - rect.top + 'px');
				el.classList.add('is-lit');
			});
			el.addEventListener('mouseleave', function () {
				el.classList.remove('is-lit');
			});
		});
	}

	/* Page transitions ------------------------------------------------------ */
	function initTransitions() {
		if (!settings.transitions || !document.querySelector('.sg-loader')) {
			return;
		}

		document.addEventListener('click', function (e) {
			if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
				return;
			}
			var link = e.target.closest('a[href]');
			if (!link || link.target === '_blank' || link.hasAttribute('download')) {
				return;
			}
			var url;
			try {
				url = new URL(link.href, window.location.href);
			} catch (err) {
				return;
			}
			if (url.origin !== window.location.origin) {
				return;
			}
			if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) {
				return;
			}
			if (/\/wp-(admin|login)|\.(pdf|zip|jpe?g|png|gif|webp|svg|mp4|docx?|xlsx?)$/i.test(url.pathname) || link.closest('#wpadminbar')) {
				return;
			}
			if (link.getAttribute('href').charAt(0) === '#' || link.dataset.elementorOpenLightbox) {
				return;
			}
			e.preventDefault();
			html.classList.add('sg-leaving');
			setTimeout(function () {
				window.location.href = url.href;
			}, 320);
		});

		window.addEventListener('pageshow', function (e) {
			if (e.persisted) {
				html.classList.remove('sg-leaving');
				html.classList.add('sg-loaded');
			}
		});
	}

	/* Chrome ----------------------------------------------------------------- */
	function initHeader() {
		var header = document.querySelector('[data-sg-header]');
		if (!header) {
			return;
		}
		var ticking = false;
		var update = function () {
			var y = window.scrollY;
			header.classList.toggle('is-scrolled', y > 10);
			ticking = false;
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
		update();
	}

	function initMobileMenu() {
		var button = document.querySelector('.sg-burger');
		var panel = document.getElementById('sg-mobile-nav');
		if (!button || !panel) {
			return;
		}
		var setOpen = function (open) {
			button.setAttribute('aria-expanded', String(open));
			button.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
			panel.hidden = !open;
			body.style.overflow = open ? 'hidden' : '';
			if (open && window.gsap && !reduce) {
				window.gsap.fromTo(panel.querySelectorAll('li, .sg-mobile__cta'), { opacity: 0, y: 18 }, { opacity: 1, y: 0, duration: 0.5, stagger: 0.04, ease: 'expo.out' });
			}
		};
		button.addEventListener('click', function () {
			setOpen(button.getAttribute('aria-expanded') !== 'true');
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
				setOpen(false);
				button.focus();
			}
		});
		window.matchMedia('(min-width: 1024px)').addEventListener('change', function (e) {
			if (e.matches) {
				setOpen(false);
			}
		});
	}

	function initProgress() {
		var bar = document.querySelector('.sg-progress');
		if (!bar) {
			return;
		}
		var ticking = false;
		var update = function () {
			var max = document.documentElement.scrollHeight - window.innerHeight;
			bar.style.setProperty('--sg-progress', max > 0 ? Math.min(1, window.scrollY / max).toFixed(4) : 0);
			ticking = false;
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
		update();
	}
})();
