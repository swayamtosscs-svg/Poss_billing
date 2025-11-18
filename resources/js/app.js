import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';
import { Chart, registerables } from 'chart.js';

window.Alpine = Alpine;
window.Swal = Swal;
window.notyf = new Notyf({
	duration: 2500,
	ripple: true,
	dismissible: true,
	position: { x: 'right', y: 'top' },
	types: [
		{ type: 'success', background: '#16a34a', icon: false },
		{ type: 'error', background: '#dc2626', icon: false },
		{ type: 'warning', background: '#f59e0b', icon: false },
		{ type: 'info', background: '#3b82f6', icon: false }
	],
});

Chart.register(...registerables);
window.Chart = Chart;

Alpine.start();
