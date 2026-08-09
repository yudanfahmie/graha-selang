(function () {
	'use strict';

	var root = document.querySelector('[data-graha-product-migration]');
	if (!root || typeof window.GrahaProductMigration !== 'object') {
		return;
	}

	var button = root.querySelector('[data-graha-migration-run]');
	var result = root.querySelector('[data-graha-migration-result]');
	var state = root.querySelector('[data-graha-migration-state]');
	if (!button || !result) {
		return;
	}

	button.addEventListener('click', function () {
		if (button.disabled) {
			return;
		}

		button.disabled = true;
		result.textContent = 'Memvalidasi bundle dan mengimpor produk…';

		var body = new URLSearchParams();
		body.set('action', window.GrahaProductMigration.action);
		body.set('nonce', window.GrahaProductMigration.nonce);

		fetch(window.GrahaProductMigration.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
			body: body.toString()
		})
			.then(function (response) { return response.json(); })
			.then(function (payload) {
				if (!payload || !payload.success) {
					throw new Error(payload && payload.data && payload.data.message ? payload.data.message : 'Migrasi gagal.');
				}
				if (state) {
					state.textContent = 'consumed';
				}
				result.textContent = payload.data.message;
				button.hidden = true;
			})
			.catch(function (error) {
				result.textContent = error.message;
				button.disabled = false;
			});
	});
}());
