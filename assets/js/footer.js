/**
 * Footer: optional “Reduced motion” preference (stored in localStorage).
 */
(function () {
	const cb = document.querySelector('.site-footer__reduce-motion input[type="checkbox"]');
	if (!cb) {
		return;
	}

	const key = 'qbb-reduced-motion';

	function apply(on) {
		document.documentElement.classList.toggle('qbb-force-reduced-motion', on);
		try {
			localStorage.setItem(key, on ? '1' : '0');
		} catch (e) {
			// ignore
		}
	}

	try {
		if (localStorage.getItem(key) === '1') {
			cb.checked = true;
			apply(true);
		}
	} catch (e) {
		// ignore
	}

	cb.addEventListener('change', function () {
		apply(cb.checked);
	});
})();
