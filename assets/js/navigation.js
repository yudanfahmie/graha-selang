(function () {
	'use strict';

	function setOpen(toggle, panel, open) {
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		panel.setAttribute('data-open', open ? 'true' : 'false');
	}

	document.querySelectorAll('[data-graha-nav]').forEach(function (nav) {
		var rootToggle = nav.querySelector('[data-graha-nav-toggle]');
		var rootPanel = nav.querySelector('[data-graha-nav-panel]');

		nav.classList.add('graha-nav--enhanced');
		nav.setAttribute('data-open', 'false');

		if (rootToggle && rootPanel) {
			rootToggle.hidden = false;
			rootToggle.addEventListener('click', function () {
				var open = rootToggle.getAttribute('aria-expanded') !== 'true';
				rootToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
				nav.setAttribute('data-open', open ? 'true' : 'false');
			});
		}

		nav.querySelectorAll('[data-graha-disclosure-toggle]').forEach(function (toggle) {
			toggle.hidden = false;
			var panelId = toggle.getAttribute('aria-controls');
			var panel = panelId ? document.getElementById(panelId) : null;
			if (!panel || !nav.contains(panel)) {
				return;
			}

			setOpen(toggle, panel, false);
			toggle.addEventListener('click', function () {
				setOpen(toggle, panel, toggle.getAttribute('aria-expanded') !== 'true');
			});
		});

		nav.addEventListener('keydown', function (event) {
			if (event.key !== 'Escape') {
				return;
			}

			nav.querySelectorAll('[data-graha-disclosure-toggle][aria-expanded="true"]').forEach(function (toggle) {
				var panel = document.getElementById(toggle.getAttribute('aria-controls'));
				if (panel) {
					setOpen(toggle, panel, false);
				}
			});

			if (rootToggle && rootToggle.getAttribute('aria-expanded') === 'true') {
				rootToggle.setAttribute('aria-expanded', 'false');
				nav.setAttribute('data-open', 'false');
				rootToggle.focus();
			}
		});
	});
}());
